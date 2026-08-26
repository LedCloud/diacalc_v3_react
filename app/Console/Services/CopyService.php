<?php

namespace App\Console\Services;

use App\Classes\Enum\DiaryRecordType;
use Illuminate\Console\Command;
use App\Models\Eating;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CopyService
{
    protected ?Collection $usersMap = null;

    protected function initUsersmap()
    {
        if ($this->usersMap === null) {
            $oldUsers = DB::connection('old_diacalc')
                ->table('backup_users')
                ->get()
                ->keyBy('email');

            $users = User::whereIn('email', $oldUsers->keys())
                ->get()
                ->keyBy('email');

            $this->usersMap = collect([]);
            foreach ($oldUsers as $email => $old_data) {
                $data = [
                    'old_data' => $old_data,
                ];
                $existing_user = $users->get($email);
                /*if ('diacalc@ya.ru' === $email) {
                    echo "Found\n";
                }*/
                if ($existing_user) {
                    $data['exist_id'] = $existing_user->id;
                    $data['user'] = $existing_user;
                }

                $this->usersMap->put($email, $data);
            }
            /*if ($this->usersMap->get('diacalc@ya.ru')) {
                echo "Here it is\n";
                $dt = $this->usersMap->get('diacalc@ya.ru');
                print_r($dt['exist_id']);
            }*/
        }
    }

    public function copyDiary(Command $command, bool $clearCurrent = false)
    {
        $this->initUsersmap();

        if ($clearCurrent) {
            $command->line(__('migration.clearing', ['type' => __('migration.types.diary')]));
            $this->clearForMappedUsers('diary');
        }

        $diary_records_count = DB::connection('old_diacalc')
            ->table('diary')
            ->count();

        $command->line(__('migration.copy', ['type' => __('migration.types.diary')]));
        $bar = $command->getOutput()->createProgressBar($diary_records_count);
        $bar->start();

        foreach ($this->usersMap as $userData) {
            $user = $this->findUser($userData);
            $userId = $user->id;

            $diary = DB::connection('old_diacalc')
                ->table('diary')
                ->where('iduser', $userData['old_data']->id)
                ->get();

            if ($diary->isEmpty()) {
                continue;
            }

            DB::transaction(function () use ($diary, $userId) {
                $this->insertDiaryComments($diary, $userId);
                $this->insertDiaryMeals($diary, $userId);
                $this->insertDiaryGlucoses($diary, $userId);
            });

            $bar->advance($diary->count());
        }

        $bar->finish();
        $command->newLine();
    }

    protected function insertDiaryComments(Collection $diary, int $userId): void
    {
        $comments = $diary
            ->where('type', DiaryRecordType::COMMENT->value)
            ->map(fn ($row) => [
                'user_id' => $userId,
                'timestamp' => $row->dt ?? now(),
                'comment' => $row->rem ?? '',
                'type' => DiaryRecordType::COMMENT->value,
            ])
            ->values()
            ->all();

        $this->bulkInsert('diary', $comments);
    }

    protected function insertDiaryMeals(Collection $diary, int $userId): void
    {
        $meals = $diary->where('type', DiaryRecordType::MEAL->value)->values();
        if ($meals->isEmpty()) {
            return;
        }

        $mealRecords = DB::connection('old_diacalc')
            ->table('diaryrecords')
            ->whereIn('owner', $meals->pluck('id'))
            ->get()
            ->groupBy('owner');

        $diaryRows = $meals->map(fn ($meal) => [
            'user_id' => $userId,
            'timestamp' => $meal->dt ?? now(),
            'type' => DiaryRecordType::MEAL->value,
            'comment' => $meal->rem ?? '',
        ])->all();

        $firstDiaryId = $this->bulkInsertReturningFirstId('diary', $diaryRows);

        $mealRows = [];
        foreach ($meals as $i => $meal) {
            $mealRows[] = [
                'diary_id' => $firstDiaryId + $i,
                'gl1' => $meal->sh1 ?? 5.6,
                'gl2' => $meal->sh2 ?? 5.6,
                'k1' => $meal->k1 ?? 1.0,
                'k2' => $meal->k2 ?? 0,
                'k3' => $meal->k3 ?? 3,
            ];
        }

        $firstMealId = $this->bulkInsertReturningFirstId('diary_meals', $mealRows);

        $productRows = [];
        foreach ($meals as $i => $meal) {
            $diaryMealId = $firstMealId + $i;
            foreach ($mealRecords->get($meal->id, collect()) as $product) {
                $productRows[] = [
                    'diary_meal_id' => $diaryMealId,
                    'name' => $product->name,
                    'prot' => $product->prot,
                    'fat' => $product->fat,
                    'carb' => $product->carb,
                    'gi' => $product->gi,
                    'weight' => $product->weight,
                ];
            }
        }

        $this->bulkInsert('diary_meal_products', $productRows);
    }

    protected function insertDiaryGlucoses(Collection $diary, int $userId): void
    {
        $glucoses = $diary->where('type', DiaryRecordType::GLUCOSE->value)->values();
        if ($glucoses->isEmpty()) {
            return;
        }

        $diaryRows = $glucoses->map(fn ($glucose) => [
            'user_id' => $userId,
            'timestamp' => $glucose->dt ?? now(),
            'comment' => $glucose->rem ?? '',
            'type' => DiaryRecordType::GLUCOSE->value,
        ])->all();

        $firstDiaryId = $this->bulkInsertReturningFirstId('diary', $diaryRows);

        $glucoseRows = [];
        foreach ($glucoses as $i => $glucose) {
            $glucoseRows[] = [
                'diary_id' => $firstDiaryId + $i,
                'gl' => $glucose->sh1,
            ];
        }

        $this->bulkInsert('diary_glucoses', $glucoseRows);
    }

    public function copyMenus(Command $command, bool $clearCurrent = false)
    {
        $this->initUsersmap();

        $menus = DB::connection('old_diacalc')
            ->table('backup_menus')
            ->get()
            ->groupBy('iduser');

        if ($clearCurrent) {
            $command->line(__('migration.clearing', ['type' => __('migration.types.menus')]));
            $this->clearForMappedUsers('menus');
        }

        $command->line(__('migration.copy', ['type' => __('migration.types.menus')]));

        $bar = $command->getOutput()->createProgressBar($this->usersMap->count());
        $bar->start();

        $now = now();
        $rows = [];

        foreach ($this->usersMap as $userData) {
            $user = $this->findUser($userData);

            foreach ($menus->get($userData['old_data']->id, collect()) as $menuItem) {
                $rows[] = [
                    'user_id' => $user->id,
                    'name' => $menuItem->name,
                    'prot' => $menuItem->prot,
                    'fat' => $menuItem->fat,
                    'carb' => $menuItem->carb,
                    'gi' => $menuItem->gi,
                    'weight' => $menuItem->weight,
                    'is_snack' => $menuItem->issnack,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $bar->advance();
        }

        $this->bulkInsert('menus', $rows);
        $bar->finish();
        $command->newLine();
    }

    public function copyProducts(Command $command, bool $clearCurrent = false)
    {
        $this->initUsersmap();

        $products = DB::connection('old_diacalc')
            ->table('backup_prods')
            ->get()
            ->groupBy('idgroup');

        $contents = DB::connection('old_diacalc')
            ->table('backup_cmpl')
            ->get()
            ->groupBy('idprod');

        $groupsByUser = DB::connection('old_diacalc')
            ->table('backup_groups')
            ->get()
            ->groupBy('iduser');

        if ($clearCurrent) {
            $command->line(__('migration.clearing', ['type' => __('migration.types.products')]));
            $this->clearForMappedUsers('product_groups');
        }

        $command->line(__('migration.copy', ['type' => __('migration.types.products')]));

        $bar = $command->getOutput()->createProgressBar($this->usersMap->count());
        $bar->start();

        foreach ($this->usersMap as $userData) {
            $user = $this->findUser($userData);
            if (!$user) {
                $bar->advance();
                continue;
            }
            $groups = $groupsByUser->get($userData['old_data']->id, collect())->values();

            if ($groups->isEmpty()) {
                $bar->advance();
                continue;
            }

            DB::transaction(function () use ($groups, $products, $contents, $user) {
                $groupRows = $groups->map(fn ($group) => [
                    'name' => $group->name,
                    'user_id' => $user->id,
                    'sort_order' => $group->sortind,
                ])->all();

                $firstGroupId = $this->bulkInsertReturningFirstId('product_groups', $groupRows);

                $productRows = [];
                $oldProductIds = [];
                foreach ($groups as $i => $group) {
                    $productGroupId = $firstGroupId + $i;
                    foreach ($products->get($group->id, collect()) as $product) {
                        $oldProductIds[] = $product->id;
                        $productRows[] = [
                            'name' => $product->name,
                            'prot' => $product->prot,
                            'fat' => $product->fat,
                            'carb' => $product->carb,
                            'gi' => $product->gi,
                            'weight' => $product->weight,
                            'used' => $product->usage,
                            'product_group_id' => $productGroupId,
                        ];
                    }
                }

                if ($productRows === []) {
                    return;
                }

                $firstProductId = $this->bulkInsertReturningFirstId('products', $productRows);

                $contentRows = [];
                foreach ($oldProductIds as $i => $oldProductId) {
                    $newProductId = $firstProductId + $i;
                    $productGroupId = $productRows[$i]['product_group_id'];
                    foreach ($contents->get($oldProductId, collect()) as $item) {
                        $contentRows[] = [
                            'name' => $item->name,
                            'prot' => $item->prot,
                            'fat' => $item->fat,
                            'carb' => $item->carb,
                            'gi' => $item->gi,
                            'weight' => $item->weight,
                            'used' => 0,
                            'product_group_id' => $productGroupId,
                            'product_id' => $newProductId,
                        ];
                    }
                }

                $this->bulkInsert('products', $contentRows);
            });

            $bar->advance();
        }

        $bar->finish();
        $command->newLine();
    }

    public function copySettings(Command $command, bool $clearCurrent = false)
    {
        $this->initUsersmap();

        /** @var Collection $settings */
        $settings = DB::connection('old_diacalc')
            ->table('settings')
            ->leftJoin('backup_users', 'settings.iduser', '=', 'backup_users.id')
            ->select('settings.*', 'backup_users.be')
            ->get()
            ->groupBy('iduser');

        if ($settings->isEmpty()) {
            return;
        }

        if ($clearCurrent) {
            $command->line(__('migration.clearing', ['type' => __('migration.types.settings')]));
            $userIds = $this->mappedUserIds();
            if ($userIds !== []) {
                DB::table('settings')
                    ->whereIn('user_id', $userIds)
                    ->where('key', 'User')
                    ->delete();
            }
        }

        $command->line(__('migration.copy', ['type' => __('migration.types.settings')]));

        $bar = $command->getOutput()->createProgressBar($settings->count());
        $bar->start();

        foreach ($this->usersMap as $userData) {
            $user = $this->findUser($userData);
            $parts = $settings->get($userData['old_data']->id, collect([]))
                ->map(fn ($old_settings) => [
                    'menu_info' => $old_settings->menuinfo,
                    'round_to' => $old_settings->roundto,
                    'is_plasma' => !(bool) $old_settings->shwhole,
                    'is_mmol' => $old_settings->mmol,
                    'target' => $old_settings->shtarget,
                    'use_freq' => $old_settings->usefreq,
                    'freq_qty' => $old_settings->freqcount,
                    'filter_off' => $old_settings->filteroff,
                    'k3_factor' => $old_settings->k3factor,
                    'weight' => $old_settings->weight,
                    'factors_by_time' => $old_settings->timedcoefs,
                    'calory_limit' => $old_settings->calorlimit,
                    'low_level' => $old_settings->shlow,
                    'high_level' => $old_settings->shhigh,
                    'be' => $old_settings->be ?? 10,
                ]);

            if ($parts->isNotEmpty()) {
                $user->putSetting('User', $parts->first());
            }
            $bar->advance();
        }

        $bar->finish();
        $command->newLine();
    }

    public function copyFactors(Command $command, bool $clearCurrent = false)
    {
        $this->initUsersmap();

        $old_ids = $this->usersMap->pluck('old_data.id')->toArray();

        $factorAll = DB::connection('old_diacalc')
            ->table('coefs')
            ->whereIn('iduser', $old_ids)
            ->get()
            ->groupBy('iduser');

        if ($clearCurrent) {
            $command->line(__('migration.clearing', ['type' => __('migration.types.factors')]));
            $this->clearForMappedUsers('factors');
        }

        $command->line(__('migration.copy', ['type' => __('migration.types.factors')]));

        $bar = $command->getOutput()->createProgressBar($this->usersMap->count());
        $bar->start();

        $rows = [];
        foreach ($this->usersMap as $userData) {
            $user = $this->findUser($userData);
            $parts = $factorAll->get($userData['old_data']->id, collect());

            foreach ($parts as $factor) {
                $rows[] = [
                    'user_id' => $user->id,
                    'time' => $factor->time,
                    'k1' => $factor->k1,
                    'k2' => $factor->k2,
                    'k3' => $factor->k3,
                ];
            }

            $bar->advance();
        }

        $this->bulkInsert('factors', $rows);
        $bar->finish();
        $command->newLine();
    }

    public function copyEatings(Command $command, bool $clearCurrent = false)
    {
        $this->initUsersmap();

        if ($clearCurrent) {
            $command->line(__('migration.clearing', ['type' => __('migration.types.eatings')]));
            $userIds = $this->mappedUserIds();
            if ($userIds !== []) {
                Eating::whereIn('user_id', $userIds)->delete();
            }
        }

        $command->line(__('migration.copy', ['type' => __('migration.types.eatings')]));

        $bar = $command->getOutput()->createProgressBar($this->usersMap->count());
        $bar->start();

        $now = now();
        $rows = [];
        foreach ($this->usersMap as $userData) {
            $user = $this->findUser($userData);
            $eatenDate = $userData['old_data']->eatendate ?? null;

            $rows[] = [
                'user_id' => $user->id,
                'k1' => $userData['old_data']->k1 ?? 1.0,
                'k2' => $userData['old_data']->k2 ?? 0.0,
                'k3' => $userData['old_data']->k3 ?? 2.0,
                'gl1' => $userData['old_data']->sh1 ?? 5.6,
                'gl2' => $userData['old_data']->sh2 ?? 5.6,
                'eaten' => $userData['old_data']->eaten ?? 0,
                'eaten_date' => $eatenDate
                    ? Carbon::createFromFormat('Y-m-d', $eatenDate)->format('Y-m-d')
                    : $now->format('Y-m-d'),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $bar->advance();
        }

        $this->bulkInsert('eatings', $rows);
        $bar->finish();
        $command->newLine();
    }

    protected function findUser($userData)
    {
        $user = $userData['user'] ?? false;

        if (empty($user) && array_key_exists('exist_id', $userData)) {
            echo "Find here " . $userData['exist_id'] . "\n";
            $user = User::findOrFail($userData['exist_id']);
        }

        if (empty($user)) {
            //we don't have user and exist_id also but we have old data which
            //includes email but it we can try to find the current user
            if (!empty($userData['old_data']->email)) {
                $user = User::where('email', $userData['old_data']->email)->first();
            } else {
                /*echo "Not found, alas\n";
                $msg = var_export($userData, true);
                throw new \Exception("User not found: " . $msg);*/
                return $user;
            }
        }

        return $user;
    }

    public function copyArchive(Command $command, bool $clearCurrent = false)
    {
        if ($clearCurrent) {
            $command->line(__('migration.clearing', ['type' => __('migration.types.archive')]));
            DB::table('arc_products')->delete();
            DB::table('arc_groups')->delete();
        }

        $command->line(__('migration.copy', ['type' => __('migration.types.archive')]));

        $groups = DB::connection('old_diacalc')
            ->table('arcgroups')
            ->select('id', 'name')
            ->get()
            ->values();

        if ($groups->isEmpty()) {
            return;
        }

        $products = DB::connection('old_diacalc')
            ->table('arcprods')
            ->select(['id', 'idgroup', 'name', 'prot', 'fat', 'carb', 'gi'])
            ->get()
            ->groupBy('idgroup');

        $bar = $command->getOutput()->createProgressBar($groups->count());
        $bar->start();

        $now = now();
        $groupRows = $groups->map(fn ($group) => [
            'name' => $group->name,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        DB::transaction(function () use ($groups, $products, $groupRows, $bar, $now) {
            $firstGroupId = $this->bulkInsertReturningFirstId('arc_groups', $groupRows);

            $productRows = [];
            foreach ($groups as $i => $group) {
                $groupId = $firstGroupId + $i;
                foreach ($products->get($group->id, collect()) as $product) {
                    $productRows[] = [
                        'group_id' => $groupId,
                        'name' => $product->name,
                        'prot' => $product->prot,
                        'fat' => $product->fat,
                        'carb' => $product->carb,
                        'gi' => $product->gi,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                $bar->advance();
            }

            $this->bulkInsert('arc_products', $productRows);
        });

        $bar->finish();
        $command->newLine();
    }

    public function copyUsers(Command $command, bool $clearCurrent = false)
    {
        $this->initUsersmap();

        if ($clearCurrent) {
            $command->line(__('migration.clearing', ['type' => __('migration.types.users')]));
            $userIds = $this->mappedUserIds();
            if ($userIds !== []) {
                User::whereIn('id', $userIds)->delete();
            }
            $this->usersMap = null;
            $this->initUsersmap();
        }

        $command->line(__('migration.copy', ['type' => __('migration.types.users')]));

        $absentUsers = $this->usersMap->filter(
            fn ($r) => !isset($r['exist_id']) && !isset($r['user'])
        );
        if ($absentUsers->isEmpty()) {
            $command->line(__('migration.no_absent_users'));
            return;
        }

        $absent_ids = $absentUsers->map(fn ($r) => $r['old_data']->id)->toArray();

        $oldUsers = DB::connection('old_diacalc')
            ->table('backup_users')
            ->whereIn('id', $absent_ids)
            ->get();

        $created = [];
        $now = now();

        foreach ($oldUsers as $oldUser) {
            $user_id = DB::table('users')->insertGetId([
                'name' => $oldUser->login,
                'email' => $oldUser->email,
                'email_verified_at' => Carbon::createFromTimestamp($oldUser->lastuse),
                'password' => $oldUser->pass,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $created[] = $user_id;

            $mappedUser = $this->usersMap->get($oldUser->email);
            if ($mappedUser) {
                $mappedUser['exist_id'] = $user_id;
                $mappedUser['id'] = $user_id;
                $this->usersMap->put($oldUser->email, $mappedUser);
            }
        }

        if (!empty($created)) {
            $users = User::whereIn('id', $created)->get()->keyBy('email');
            $this->usersMap = $this->usersMap->map(function ($record, $email) use ($users) {
                $u = $users->get($email);
                if ($u) {
                    $record['user'] = $u;
                    $record['exist_id'] = $u->id;
                }
                return $record;
            });
        }
    }

    protected function mappedUserIds(): array
    {
        return $this->usersMap
            ->pluck('exist_id')
            ->filter()
            ->values()
            ->all();
    }

    protected function clearForMappedUsers(string $table): void
    {
        $userIds = $this->mappedUserIds();
        if ($userIds === []) {
            return;
        }

        DB::table($table)->whereIn('user_id', $userIds)->delete();
    }

    /**
     * Multi-row insert; returns the first AUTO_INCREMENT id (MySQL consecutive block).
     */
    protected function bulkInsertReturningFirstId(string $table, array $rows): int
    {
        DB::table($table)->insert($rows);

        return (int) DB::getPdo()->lastInsertId();
    }

    protected function bulkInsert(string $table, array $rows, int $chunkSize = 500): void
    {
        if ($rows === []) {
            return;
        }

        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            DB::table($table)->insert($chunk);
        }
    }
}
