<main class="content__main">
    <h2 class="content__main-heading">Add project</h2>

    <form class="form"  action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <div class="form__row">
            <label class="form__label" for="project_name">Title <sup>*</sup></label>

            <input
                class="form__input <?=
                    isset($errors['name'])
                    ? 'form__input--error'
                    : ''
                ?>"
                type="text"
                name="name"
                id="project_name"
                value=""
                placeholder="Enter your title">
            <p class="form__message"><?=
                    isset($errors['name'])
                    ? $errors['name']
                    : ''
                ?>
            </p>

        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Add">
        </div>
    </form>
</main>
