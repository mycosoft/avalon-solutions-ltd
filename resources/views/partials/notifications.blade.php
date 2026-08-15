<script>
function fetchNotifications() {
    var navbarRight = document.querySelector('.navbar-nav.ml-auto');
    if (!navbarRight) return;

    var notificationMenu = document.getElementById('notificationMenu');
    if (!notificationMenu) {
        var notificationHtml = `
        <li class="nav-item dropdown" id="notificationMenu">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="far fa-bell" style="font-size: 1.2rem;"></i>
                <span class="badge badge-danger navbar-badge d-none" id="notificationCount">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="max-height: 400px; overflow-y: auto; overflow-x: hidden; min-width: 300px;">
                <span class="dropdown-item dropdown-header"><span id="notificationHeaderCount">0</span> Unread Notifications</span>
                <div id="notificationList">
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item text-center text-muted text-sm" style="padding: 10px;">No new notifications</a>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
        `;
        navbarRight.insertAdjacentHTML('afterbegin', notificationHtml);
    }

    fetch('{{ route("notifications.unread") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        var countBadge = document.getElementById('notificationCount');
        var headerCount = document.getElementById('notificationHeaderCount');
        var notificationList = document.getElementById('notificationList');

        if (countBadge) {
            if (data.count > 0) {
                countBadge.textContent = data.count;
                countBadge.classList.remove('d-none');
            } else {
                countBadge.classList.add('d-none');
            }
        }

        if (headerCount) {
            headerCount.textContent = data.count;
        }

        if (notificationList && data.notifications.length > 0) {
            var html = '';
            data.notifications.forEach(function(notif) {
                var iconClass = 'info';
                var iconHtml = '<i class="fas fa-info-circle text-info mr-2"></i>';
                if (notif.type === 'alert') {
                    iconClass = 'danger';
                    iconHtml = '<i class="fas fa-exclamation-triangle text-danger mr-2"></i>';
                } else if (notif.type === 'warning') {
                    iconClass = 'warning';
                    iconHtml = '<i class="fas fa-exclamation-circle text-warning mr-2"></i>';
                } else if (notif.type === 'success') {
                    iconClass = 'success';
                    iconHtml = '<i class="fas fa-check-circle text-success mr-2"></i>';
                }

                var timeAgo = notif.created_at ? timeSince(notif.created_at) : 'just now';
                html += `
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item notification-item" data-id="${notif.id}" onclick="markAsRead(${notif.id}, this, event)">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="dropdown-item-title" style="font-size: 0.95rem;">
                                    ${iconHtml}
                                    ${notif.title}
                                </h3>
                                <p class="text-sm text-muted mb-0" style="margin-left: 22px;">${notif.message}</p>
                                <p class="text-xs text-muted mb-0" style="margin-left: 22px;">${timeAgo}</p>
                            </div>
                        </div>
                    </a>
                `;
            });
            notificationList.innerHTML = html;
        } else if (notificationList) {
            notificationList.innerHTML = '<div class="dropdown-divider"></div><a href="#" class="dropdown-item text-center text-muted text-sm" style="padding: 10px;">No new notifications</a>';
        }
    })
    .catch(error => console.log('Notification fetch error:', error));
}

function markAsRead(id, element, event) {
    event.preventDefault();

    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            fetchNotifications();
        }
    })
    .catch(error => console.log('Mark as read error:', error));
}

function timeSince(dateString) {
    var date = new Date(dateString);
    var seconds = Math.floor((new Date() - date) / 1000);
    var intervals = {
        'year': 31536000,
        'month': 2592000,
        'week': 604800,
        'day': 86400,
        'hour': 3600,
        'minute': 60,
        'second': 1
    };

    for (var key in intervals) {
        var interval = Math.floor(seconds / intervals[key]);
        if (interval >= 1) {
            return interval + ' ' + key + (interval > 1 ? 's' : '') + ' ago';
        }
    }
    return 'just now';
}

document.addEventListener('DOMContentLoaded', function() {
    fetchNotifications();
    setInterval(fetchNotifications, 10000);
});
</script>
