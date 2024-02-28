(function() {
    let seconds = 0;
    let secondsDisplayed = 0;
    let minutesDisplayed = 0;
    let interval = null;
    const initialDocumentTitle = document.title;
    const timerHours = document.getElementById('timer-hours');
    const timerMinutes = document.getElementById('timer-minutes');
    const timerSeconds = document.getElementById('timer-seconds');
    const startButton = document.getElementById('timer-start');
    const stopButton = document.getElementById('timer-stop');
    const resetButton = document.getElementById('timer-reset');

    const notification = document.getElementById('notification');
    const issueInput = document.getElementById('issue-input');
    const issueDialog = document.getElementById('issue-dialog');
    const issueList = document.getElementById('issue-list');
    const hoursInput = document.getElementById('hours-input');

    if (notification) {
        setTimeout(function() {
            notification.remove();
        }, 5000);
    }

    issueInput.addEventListener('click', function() {
        if (issueList.children.length && !issueDialog.open) {
            issueDialog.open = 'open';
        } else {
            issueDialog.close();
        }
    });

    issueList.addEventListener('click', function(event) {
        event.preventDefault();

        if (event.target.matches('li')) {
            issueInput.value = event.target.dataset.id;
            issueDialog.close();
        }
    });

    startButton.addEventListener('click', function() {
        interval = setInterval(function() {
            ++seconds;
            ++secondsDisplayed;
            if (secondsDisplayed === 60) {
                secondsDisplayed = 0;
                ++minutesDisplayed;
            }

            if (minutesDisplayed === 60) {
                minutesDisplayed = 0;
            }

            timerSeconds.innerHTML = String(secondsDisplayed).padStart(2, '0');
            timerMinutes.innerHTML = String(minutesDisplayed).padStart(2, '0');
            timerHours.innerHTML = String(Math.floor(seconds / 60 / 60)).padStart(2, '0');
            hoursInput.value = Math.round(seconds / 60 / 60 * 100) / 100;
            document.title = timerHours.innerHTML + ':' + timerMinutes.innerHTML + ':' + timerSeconds.innerHTML;
        }, 1000);
    })

    stopButton.addEventListener('click', function() {
        if (interval) {
            clearInterval(interval);
        }
    });

    resetButton.addEventListener('click', function() {
        reset();
    });

    function reset()
    {
        if (interval) {
            clearInterval(interval);
        }
        seconds = 0;
        secondsDisplayed = 0;
        timerSeconds.innerHTML = '00';
        timerMinutes.innerHTML = '00';
        timerHours.innerHTML = '00';
        hoursInput.value = '';
        document.title = initialDocumentTitle;
    }
})();
