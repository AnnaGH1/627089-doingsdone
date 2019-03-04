<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a
            href="/"
            class="tasks-switch__item">Все задачи</a>
        <a
            href="index.php?dt_due=today"
            class="tasks-switch__item
            <?=
                $_GET['dt_due'] === 'today'
                ? 'tasks-switch__item--active'
                : ''
            ?>">Повестка дня</a>
        <a
            href="index.php?dt_due=tomorrow"
            class="tasks-switch__item
            <?=
                $_GET['dt_due'] === 'tomorrow'
                ? 'tasks-switch__item--active'
                : ''
            ?>">Завтра</a>
        <a
            href="/"
            class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input class="checkbox__input visually-hidden show_completed" type="checkbox"
            <?=
                $show_complete_tasks === 1
                ? 'checked'
                : ''
            ?>
        >
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php foreach ($task_items as $task_item): ?>
        <?php if($show_complete_tasks === 1 || $task_item['dt_complete'] === null): ?>
            <tr class="tasks__item task
            <?=
                $task_item['dt_complete']
                ? 'task--completed'
                : ''
            ?>
            <?=
                is_task_important($task_item)
                ? 'task--important'
                : ''
            ?>"
            >
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input
                            class="checkbox__input visually-hidden task__checkbox"
                            type="checkbox"
                            value="<?=
                                $task_item['id']
                            ?>"
                            <?=
                                $task_item['dt_complete'] === true
                                ? 'checked'
                                : ''
                            ?>
                        >
                        <span class="checkbox__text">
                            <?=strip_tags($task_item['name']);?>
                        </span>
                    </label>
                </td>

                <td class="task__file">
                    <?php if ($task_item['file'] !== null): ?>
                        <a class="download-link" href="<?=strip_tags($task_item['file']);?>"></a>
                    <?php endif; ?>
                </td>

                <td class="task__date">
                    <?=strip_tags($task_item['due']);?>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
