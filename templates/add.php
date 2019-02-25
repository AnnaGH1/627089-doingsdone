<main class="content__main">
    <h2 class="content__main-heading">Добавление задачи</h2>

    <form class="form"
          action="<?php echo $_SERVER['PHP_SELF']?>"
          enctype="multipart/form-data"
          method="POST">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input
                class="form__input"
                type="text"
                name="name"
                id="name"
                value="<?=
                            isset($_POST['name'])
                            ? strip_tags($_POST['name'])
                            : ''
                        ?>"
                placeholder="Введите название"
            >
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект</label>

            <select
                class="form__input form__input--select"
                name="project"
                id="project">
                    <option value="" selected>Без проекта</option>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?=$category['id'];?>"
                        <?=
                            (isset($_POST['project']) && intval($_POST['project']) === $category['id'])
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?=$category['name'];?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input
                class="form__input form__input--date"
                type="date"
                name="date"
                id="date"
                value="<?=
                            isset($_POST['date'])
                            ? strip_tags($_POST['date'])
                            : ''
                        ?>"
                placeholder="Введите дату в формате ДД.ММ.ГГГГ">
        </div>

        <div class="form__row">
            <label class="form__label" for="preview">Файл</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview" value="">

                <label class="button button--transparent" for="preview">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>
