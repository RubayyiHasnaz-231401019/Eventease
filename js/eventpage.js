const followButton = document.querySelector('.follow');


if (localStorage.getItem('followStatus') === 'unfollow') {
    followButton.classList.add('unfollow');
    followButton.classList.remove('follow');
    followButton.textContent = 'Unfollow';
}


followButton.addEventListener('click', function () {
    if (followButton.classList.contains('follow')) {
        followButton.classList.remove('follow');
        followButton.classList.add('unfollow');
        followButton.textContent = 'Unfollow';
        localStorage.setItem('followStatus', 'unfollow');
    } else {
        followButton.classList.remove('unfollow');
        followButton.classList.add('follow');
        followButton.textContent = '+ Follow';
        localStorage.setItem('followStatus', 'follow');
    }
});

let isAvailable = false;

    function toggleAvailability() {
        isAvailable = !isAvailable;

        const availabilityDiv = document.getElementById('ticketAvailability');
        
        if (isAvailable) {
            availabilityDiv.textContent = "Tickets Available";
            availabilityDiv.classList.remove('unavailable');
            availabilityDiv.classList.add('available');
        } else {
            availabilityDiv.textContent = "Tickets Unavailable";
            availabilityDiv.classList.remove('available');
            availabilityDiv.classList.add('unavailable');
        }
    }
