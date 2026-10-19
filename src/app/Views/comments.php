<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-name" content="<?= esc($csrfName) ?>">
    <meta name="csrf-hash" content="<?= esc($csrfHash) ?>">
    <title>Комментарии</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/comments.css') ?>">
</head>
<body>
    <main class="site-shell">
        <header class="site-header">
            <span class="brand-mark" aria-hidden="true">///</span>
            <span class="brand-name">Открытый разговор</span>
        </header>

        <div class="page-heading">
            <div>
                <span class="eyebrow">Обсуждение</span>
                <h1>Комментарии<span class="heading-dot">.</span></h1>
                <p class="intro">Прочитайте сообщения и поделитесь своим мнением.</p>
            </div>
            <div class="count-block" aria-live="polite">
                <strong id="comment-count">—</strong>
                <span>всего записей</span>
            </div>
        </div>

        <section class="discussion" aria-labelledby="list-title">
            <div class="section-toolbar">
                <h2 id="list-title">Все сообщения</h2>
                <div class="sort-controls">
                    <label for="sort">Сортировать</label>
                    <select id="sort" class="form-control" aria-label="Поле сортировки">
                        <option value="date">По дате добавления</option>
                        <option value="id">По ID</option>
                    </select>
                    <select id="direction" class="form-control" aria-label="Направление сортировки">
                        <option value="desc">Сначала новые</option>
                        <option value="asc">Сначала старые</option>
                    </select>
                </div>
            </div>

            <div id="notice" class="notice" role="status" aria-live="polite" hidden></div>
            <div id="comments-list" class="comments-list" aria-live="polite">
                <p class="list-state">Загружаем комментарии…</p>
            </div>
            <nav id="pagination" class="pagination-wrap" aria-label="Страницы комментариев"></nav>
        </section>

        <section class="composer" aria-labelledby="form-title">
            <div class="composer-intro">
                <span class="eyebrow">Ваше слово</span>
                <h2 id="form-title">Оставить комментарий</h2>
                <p>Укажите почту, выберите дату и напишите сообщение.</p>
            </div>
            <form id="comment-form" novalidate>
                <div class="form-row">
                    <div class="form-group col-md-7">
                        <label for="name">Электронная почта <span aria-hidden="true">*</span></label>
                        <input type="email" class="form-control" id="name" name="name" maxlength="254" autocomplete="email" required aria-describedby="name-error">
                        <div class="field-error" id="name-error"></div>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="date">Дата комментария <span aria-hidden="true">*</span></label>
                        <input type="date" class="form-control" id="date" name="date" required aria-describedby="date-error">
                        <div class="field-error" id="date-error"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="text">Сообщение <span aria-hidden="true">*</span></label>
                    <textarea class="form-control" id="text" name="text" rows="4" maxlength="2000" required aria-describedby="text-error"></textarea>
                    <div class="field-error" id="text-error"></div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-button" id="submit-button">Опубликовать <span aria-hidden="true">↗</span></button>
                    <span>Поля со звёздочкой обязательны</span>
                </div>
            </form>
        </section>
        <footer class="site-footer">Открытый разговор <span>© <?= date('Y') ?></span></footer>
    </main>

    <script src="<?= base_url('assets/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/comments.js') ?>"></script>
</body>
</html>
