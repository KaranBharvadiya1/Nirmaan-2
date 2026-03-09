@php
    $badgeConfig = $layoutMessagingBadgeConfig ?? [];
@endphp

@if (($badgeConfig['enabled'] ?? false))
<script type="module">
    import { getApp, getApps, initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js';
    import { getAuth, signInWithCustomToken } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js';
    import { collection, getFirestore, onSnapshot, query, where } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-firestore.js';

    const badgeConfig = @json($badgeConfig);
    const messageBadgeElements = Array.from(document.querySelectorAll('[data-message-badge]'));

    function renderMessageBadge(count) {
        messageBadgeElements.forEach((element) => {
            if (!count) {
                element.textContent = '0';
                element.classList.add('d-none');
                return;
            }

            element.textContent = String(count);
            element.classList.remove('d-none');
        });
    }

    async function bootMessageBadges() {
        if (!messageBadgeElements.length || !badgeConfig.enabled) {
            return;
        }

        try {
            const response = await fetch(badgeConfig.firebaseTokenEndpoint, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            const payload = await response.json();
            if (!response.ok || !payload.token) {
                renderMessageBadge(0);
                return;
            }

            const app = getApps().length ? getApp() : initializeApp(badgeConfig.firebaseClientConfig);
            const auth = getAuth(app);
            await signInWithCustomToken(auth, payload.token);

            const unreadField = badgeConfig.currentUserRole === 'Owner'
                ? 'unread_owner_count'
                : 'unread_contractor_count';

            const db = getFirestore(app);
            const conversationsQuery = query(
                collection(db, 'conversations'),
                where('participants', 'array-contains', badgeConfig.currentUserFirebaseUid)
            );

            onSnapshot(conversationsQuery, (snapshot) => {
                let unreadCount = 0;

                snapshot.forEach((conversationDoc) => {
                    const data = conversationDoc.data();
                    unreadCount += Number(data?.[unreadField] || 0);
                });

                renderMessageBadge(unreadCount);
            }, () => {
                renderMessageBadge(0);
            });
        } catch (_error) {
            renderMessageBadge(0);
        }
    }

    bootMessageBadges();
</script>
@endif
