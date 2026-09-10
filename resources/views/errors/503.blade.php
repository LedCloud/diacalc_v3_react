<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>{{ __('maintenance.title') }} — DiaCalc</title>
        <style>
            :root {
                --text: #275870;
                --muted: #4a7a90;
                --bg: #fbfeff;
                --card: #ffffff;
                --border: #bce8f1;
                --header: #d9edf7;
                --accent: #1a88ff;
            }

            * { box-sizing: border-box; }

            html, body {
                margin: 0;
                min-height: 100%;
            }

            body {
                font-family: system-ui, -apple-system, "Segoe UI", Roboto, Ubuntu, sans-serif;
                background: var(--bg);
                color: var(--text);
                line-height: 1.5;
            }

            .page {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }

            .card {
                width: 100%;
                max-width: 36rem;
                background: var(--card);
                border: 1px solid var(--border);
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 8px 24px rgba(39, 88, 112, 0.08);
            }

            .card__header {
                background: var(--header);
                padding: 1rem 1.5rem;
                border-bottom: 1px solid var(--border);
            }

            .brand {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 700;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            .card__body {
                padding: 1.5rem;
            }

            h1 {
                margin: 0 0 0.75rem;
                font-size: 1.5rem;
                font-weight: 650;
            }

            p {
                margin: 0;
                color: var(--muted);
                font-size: 1.05rem;
            }

            .copy + .copy {
                margin-top: 1.25rem;
                padding-top: 1.25rem;
                border-top: 1px solid var(--border);
            }

            body.is-localized .copy { display: none; }
            body.is-localized .copy.is-active { display: block; }
            body.is-localized .copy + .copy {
                margin-top: 0;
                padding-top: 0;
                border-top: 0;
            }

            .status {
                display: inline-block;
                margin-top: 1.25rem;
                color: var(--accent);
                font-size: 0.8rem;
                font-weight: 600;
                letter-spacing: 0.06em;
            }
        </style>
    </head>
    <body>
        <main class="page">
            <article class="card">
                <header class="card__header">
                    <p class="brand">DiaCalc</p>
                </header>
                <div class="card__body">
                    <section class="copy" data-locale="en" lang="en">
                        <h1>{{ trans('maintenance.heading', [], 'en') }}</h1>
                        <p>{{ trans('maintenance.body', [], 'en') }}</p>
                    </section>
                    <section class="copy" data-locale="ru" lang="ru">
                        <h1>{{ trans('maintenance.heading', [], 'ru') }}</h1>
                        <p>{{ trans('maintenance.body', [], 'ru') }}</p>
                    </section>
                    <span class="status">503</span>
                </div>
            </article>
        </main>
        <script>
            (function () {
                var supported = ['ru', 'en'];
                var nav = String(navigator.language || 'en').slice(0, 2).toLowerCase();
                var locale = supported.indexOf(nav) !== -1 ? nav : 'en';
                document.documentElement.lang = locale;
                document.body.classList.add('is-localized');
                document.querySelectorAll('.copy').forEach(function (node) {
                    node.classList.toggle('is-active', node.getAttribute('data-locale') === locale);
                });
            })();
        </script>
    </body>
</html>
