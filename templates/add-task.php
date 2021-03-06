<main class="content__main">
    <h2 class="content__main-heading">Add task</h2>

    <form class="form"
          action="<?php echo $_SERVER['PHP_SELF']?>"
          enctype="multipart/form-data"
          method="POST">
        <div class="form__row">
            <label class="form__label" for="name">Title <sup>*</sup></label>

            <input
                class="form__input <?=
                    isset($errors['name'])
                    ? 'form__input--error'
                    : ''
                ?>"
                type="text"
                name="name"
                id="name"
                value="<?=
                            isset($_POST['name'])
                            ? strip_tags($_POST['name'])
                            : ''
                        ?>"
                placeholder="Enter your title"
            >
            <p class="form__message"><?=
                isset($errors['name'])
                    ? $errors['name']
                    : ''
                ?>
            </p>
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Project</label>

            <select
                class="form__input form__input--select <?=
                                                            isset($errors['project'])
                                                            ? 'form__input--error'
                                                            : ''
                                                        ?>"
                name="project"
                id="project">
                    <option value="">No project</option>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?=$category['id'];?>"
                        <?=
                            (isset($_POST['project']) && intval($_POST['project']) === $category['id'])
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?=strip_tags($category['name']);?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="form__message"><?=
                isset($errors['project'])
                    ? $errors['project']
                    : ''
                ?>
            </p>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Due date</label>

            <input
                class="form__input form__input--date <?=
                                                    isset($errors['date'])
                                                    ? 'form__input--error'
                                                    : ''
                                                    ?>"
                type="date"
                name="date"
                id="date"
                value="<?=
                            isset($_POST['date'])
                            ? strip_tags($_POST['date'])
                            : ''
                        ?>"
                placeholder="Enter a date">
            <p class="form__message"><?=
                isset($errors['date'])
                    ? $errors['date']
                    : ''
                ?>
            </p>
        </div>

        <div class="form__row">
            <label class="form__label" for="preview">File</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview" value="">

                <label class="button button--transparent" for="preview">
                    <span>Choose your file</span>
                </label>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Add">
        </div>
    </form>
</main>
