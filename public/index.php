<?php

use function App\getThisWeekRegisteredHours;
use function App\getTodayRegisteredHours;
use function App\registerTime;

require_once dirname(__DIR__).'/vendor/autoload.php';

$config = require dirname(__DIR__).'/config.php';
$timeRegistered = false;
$error = null;

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    $timeRegistered = true;
    $error = registerTime($config);
}

$todayHours = getTodayRegisteredHours($config);
$thisWeekHours = getThisWeekRegisteredHours($config);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Redmine Timer</title>
        <link rel="stylesheet" href="main.css" />
    </head>
    <body>
        <main>
            <h1>Redmine Timer</h1>

            <?php if ($timeRegistered) { ?>
                <?php if ($error) { ?>
                    <p id="notification" class="notification error"><?php echo $error; ?></p>
                <?php } else { ?>
                    <p id="notification" class="notification success">Saved</p>
                <?php } ?>
            <?php } ?>

            <div class="timer">
                <div class="time">
                    <span id="timer-hours">00</span>:<span id="timer-minutes">00</span>:<span id="timer-seconds">00</span>
                </div>
                <button id="timer-start">Start</button>
                <button id="timer-stop">Stop</button>
                <button id="timer-reset">Reset</button>
            </div>

            <form method="post" action="/">
                <div class="form-content">
                    <div>
                        <input type="number" id="issue-input" name="issue_id" placeholder="Issue #" required="required" autocomplete="off" />
                        <dialog id="issue-dialog">
                            <p>Favorites</p>
                            <ul id="issue-list">
                                <?php foreach ($config['defaults']['issue_ids'] as $id => $name) { ?>
                                <li data-id="<?php echo $id; ?>"><?php echo $name; ?></li>
                                <?php } ?>
                            </ul>
                        </dialog>
                    </div>
                    <div>
                        <input type="number" id="hours-input" name="hours" min="0" step="0.01" placeholder="Hours" required="required" />
                    </div>
                    <div>
                        <input type="date" name="spent_on" placeholder="Date" value="<?php echo date('Y-m-d'); ?>" required="required" />
                    </div>
                    <div>
                        <select name="activity_id" required="required">
                            <?php foreach ($config['redmine']['activities'] as $id => $name) { ?>
                            <option value="<?php echo $id; ?>"<?php echo $id === $config['defaults']['activity_id'] ? ' selected="selected"' : ''; ?>><?php echo $name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <textarea name="comments" placeholder="Comment"></textarea>
                    </div>
                    <div>
                        <input type="submit" value="Save" />
                    </div>
                </div>
            </form>
        </main>
        <footer>
            <p class="text-center"><?php echo $todayHours; ?> hour<?php echo $todayHours >= 2 ? 's' : ''; ?> registered today</p>

            <table>
                <caption>This week</caption>
                <tbody>
            <?php foreach ($thisWeekHours as $day => $hours): ?>
                <tr>
                    <td class="text-left"><?php echo (new DateTime($day))->format('l j F') ?></td>
                    <td class="text-right"><?php echo $hours ?></td>
                </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        </footer>
        <script src="main.js"></script>
    </body>
</html>
