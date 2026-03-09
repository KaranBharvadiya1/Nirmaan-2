@push('styles')
<style>
    :root {
        --nrmn-sidebar: #0f2f73;
        --nrmn-sidebar-soft: #12439f;
        --nrmn-sidebar-head: #0a245a;
        --nrmn-accent: #2452e6;
        --nrmn-accent-soft: #dbe8ff;
        --nrmn-bubble-me: #e3ecff;
        --nrmn-bubble-me-border: #b8c9f6;
        --nrmn-bubble-other: #ffffff;
        --nrmn-bubble-other-border: #d4deef;
        --nrmn-chat-bg: #eef3ff;
        --nrmn-chat-border: #cfe0ff;
        --nrmn-chat-text: #0f172a;
        --nrmn-muted: #5f6e93;
    }

    .wa-shell {
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.12);
        border: 1px solid rgba(36, 82, 230, 0.2);
        background: #f9fbff;
    }

    .wa-layout {
        min-height: 72vh;
    }

    .wa-sidebar {
        background: linear-gradient(180deg, var(--nrmn-sidebar) 0%, var(--nrmn-sidebar-soft) 100%);
        color: #eef4ff;
        display: flex;
        flex-direction: column;
        border-right: 1px solid rgba(255, 255, 255, 0.16);
    }

    .wa-sidebar-head {
        padding: 0.9rem 1rem;
        background: rgba(8, 28, 73, 0.72);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .wa-count-chip {
        background: #9fd0ff;
        color: #0a255a;
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 0.15rem 0.55rem;
        min-width: 1.8rem;
        text-align: center;
    }

    .wa-unread-badge {
        min-width: 1.3rem;
        height: 1.3rem;
        padding: 0 0.35rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ffcf6b;
        color: #0a255a;
        font-size: 0.68rem;
        font-weight: 700;
        margin-left: 0.35rem;
    }

    .wa-search-wrap {
        padding: 0.7rem 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.16);
    }

    .wa-search-input {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        border-radius: 0.7rem;
    }

    .wa-search-input::placeholder {
        color: rgba(255, 255, 255, 0.75);
    }

    .wa-search-input:focus {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border-color: #9fd0ff;
        box-shadow: 0 0 0 0.2rem rgba(159, 208, 255, 0.25);
    }

    .wa-conversation-list {
        overflow-y: auto;
        flex: 1;
    }

    .wa-conversation-item {
        width: 100%;
        border: 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        background: transparent;
        text-align: left;
        color: inherit;
        padding: 0.75rem 0.8rem;
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        transition: background-color 0.2s ease;
    }

    .wa-conversation-item:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    .wa-conversation-item.active {
        background: rgba(255, 255, 255, 0.2);
    }

    .wa-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(145deg, #82b4ff, #5f8deb);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        color: #0d2f73;
        flex-shrink: 0;
    }

    .wa-avatar-image {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.38);
        background: #fff;
    }

    .wa-conv-main {
        flex: 1;
        min-width: 0;
    }

    .wa-conv-top {
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        align-items: baseline;
    }

    .wa-conv-name {
        margin: 0;
        font-weight: 600;
        font-size: 0.9rem;
        color: #f3f7ff;
    }

    .wa-conv-time {
        font-size: 0.72rem;
        color: #c9dcff;
        white-space: nowrap;
    }

    .wa-conv-project {
        margin: 0.16rem 0 0;
        color: #c6d9ff;
        font-size: 0.74rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .wa-conv-preview {
        margin: 0.18rem 0 0;
        color: #e7efff;
        font-size: 0.79rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .wa-chip {
        display: inline-block;
        margin-top: 0.22rem;
        border-radius: 999px;
        font-size: 0.68rem;
        padding: 0.15rem 0.46rem;
        background: rgba(159, 208, 255, 0.2);
        color: #cde3ff;
    }

    .wa-chat-panel {
        display: flex;
        flex-direction: column;
        background: var(--nrmn-chat-bg);
    }

    .wa-chat-head {
        background: #ffffff;
        color: var(--nrmn-chat-text);
        border-bottom: 1px solid var(--nrmn-chat-border);
        padding: 0.8rem 1rem;
    }

    .wa-chat-title {
        margin: 0;
        font-size: 0.96rem;
        font-weight: 600;
    }

    .wa-chat-meta {
        margin: 0.15rem 0 0;
        color: var(--nrmn-muted);
        font-size: 0.76rem;
    }

    .wa-chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background-color: #ece5dd;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140' viewBox='0 0 140 140'%3E%3Cg fill='none' stroke='%23d6cec4' stroke-width='1.25' stroke-linecap='round' stroke-linejoin='round' opacity='0.55'%3E%3Ccircle cx='16' cy='17' r='6'/%3E%3Ccircle cx='103' cy='22' r='5'/%3E%3Cpath d='M38 13h17a5 5 0 0 1 5 5v9a5 5 0 0 1-5 5H45l-5 5v-5h-2a5 5 0 0 1-5-5v-9a5 5 0 0 1 5-5z'/%3E%3Crect x='76' y='48' width='20' height='14' rx='3'/%3E%3Cpath d='M80 48v-3a6 6 0 1 1 12 0v3'/%3E%3Cpath d='M15 74l7 5-2 8 7-5 7 5-2-8 7-5h-9l-3-8-3 8z'/%3E%3Cpath d='M102 86a7 7 0 0 1 12-5 7 7 0 1 1 3 11l-6 8'/%3E%3Cpath d='M43 104h18a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H50l-5 4v-4h-2a4 4 0 0 1-4-4v-10a4 4 0 0 1 4-4z'/%3E%3Cpath d='M89 112h14'/%3E%3Cpath d='M96 105v14'/%3E%3C/g%3E%3C/svg%3E");
        background-size: 140px 140px;
    }

    .wa-system-note {
        color: var(--nrmn-muted);
        font-size: 0.84rem;
    }

    .wa-message-row {
        display: flex;
        margin-bottom: 0.5rem;
        align-items: flex-end;
    }

    .wa-message-row.me {
        justify-content: flex-end;
    }

    .wa-bubble {
        display: inline-flex;
        align-items: flex-end;
        gap: 0.34rem;
        width: fit-content;
        max-width: min(58%, 460px);
        border-radius: 0.45rem;
        padding: 0.28rem 0.42rem 0.25rem;
        line-height: 1.22;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.12);
        font-size: 0.84rem;
        position: relative;
    }

    .wa-message-row.me .wa-bubble {
        background: var(--nrmn-bubble-me);
        color: var(--nrmn-chat-text);
        border: 1px solid var(--nrmn-bubble-me-border);
        border-radius: 0.45rem 0.45rem 0.2rem 0.45rem;
    }

    .wa-message-row.other .wa-bubble {
        background: var(--nrmn-bubble-other);
        color: var(--nrmn-chat-text);
        border: 1px solid var(--nrmn-bubble-other-border);
        border-radius: 0.45rem 0.45rem 0.45rem 0.2rem;
    }

    .wa-msg-text {
        display: inline;
        white-space: pre-wrap;
        word-break: break-word;
        line-height: 1.22;
        margin: 0;
    }

    .wa-time {
        display: inline-flex;
        align-items: center;
        font-size: 0.66rem;
        color: #61718f;
        white-space: nowrap;
        line-height: 1;
        transform: translateY(1px);
    }

    .wa-message-row.me .wa-bubble::after {
        content: '';
        position: absolute;
        right: -5px;
        bottom: 1px;
        width: 10px;
        height: 11px;
        background: var(--nrmn-bubble-me);
        border-right: 1px solid var(--nrmn-bubble-me-border);
        border-bottom: 1px solid var(--nrmn-bubble-me-border);
        clip-path: polygon(0 0, 100% 48%, 0 100%);
    }

    .wa-message-row.other .wa-bubble::before {
        content: '';
        position: absolute;
        left: -5px;
        bottom: 1px;
        width: 10px;
        height: 11px;
        background: var(--nrmn-bubble-other);
        border-left: 1px solid var(--nrmn-bubble-other-border);
        border-bottom: 1px solid var(--nrmn-bubble-other-border);
        clip-path: polygon(100% 0, 0 48%, 100% 100%);
    }

    .wa-composer {
        background: #f6f9ff;
        border-top: 1px solid var(--nrmn-chat-border);
        padding: 0.7rem 0.75rem 0.55rem;
    }

    .wa-composer-form {
        display: flex;
        gap: 0.55rem;
        align-items: center;
    }

    .wa-input {
        border-radius: 999px;
        border: 1px solid #cbd5dc;
        padding: 0.58rem 0.9rem;
        font-size: 0.9rem;
    }

    .wa-input:focus {
        border-color: var(--nrmn-accent);
        box-shadow: 0 0 0 0.2rem rgba(36, 82, 230, 0.16);
    }

    .wa-send-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--nrmn-accent);
        color: #fff;
        font-size: 1rem;
    }

    .wa-send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .wa-status {
        margin: 0.44rem 0 0;
        font-size: 0.76rem;
        color: var(--nrmn-muted);
    }

    .wa-mobile-back {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid rgba(36, 82, 230, 0.25);
        background: transparent;
        color: #2452e6;
    }

    @media (max-width: 991.98px) {
        .wa-layout {
            min-height: 76vh;
        }

        .wa-shell.wa-mobile-chat-active .wa-sidebar {
            display: none;
        }

        .wa-shell:not(.wa-mobile-chat-active) .wa-chat-panel {
            display: none;
        }

        .wa-chat-panel {
            min-height: 76vh;
        }

        .wa-bubble {
            max-width: 80%;
        }
    }
</style>
@endpush

<div class="wa-shell" id="waMessagingShell">
    <div class="row g-0 wa-layout">
        <aside class="col-12 col-lg-4 wa-sidebar">
            <div class="wa-sidebar-head">
                <p class="mb-0 fw-semibold">Chats</p>
                <span class="wa-count-chip" id="conversationCountBadge">0</span>
            </div>

            <div class="wa-search-wrap">
                <input
                    id="conversationSearchInput"
                    type="text"
                    class="form-control wa-search-input"
                    placeholder="Search name or project"
                    autocomplete="off"
                >
            </div>

            <div class="wa-conversation-list" id="conversationList"></div>
        </aside>

        <section class="col-12 col-lg-8 wa-chat-panel">
            <header class="wa-chat-head">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="d-flex align-items-start gap-2">
                        <button type="button" class="wa-mobile-back d-lg-none" id="mobileBackButton" aria-label="Back to chats">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <div>
                            <p class="wa-chat-title" id="chatTitle">Select a conversation</p>
                            <p class="wa-chat-meta" id="chatMeta">Choose a thread to start messaging.</p>
                        </div>
                    </div>
                    <a href="#" target="_blank" class="btn btn-outline-primary btn-sm d-none" id="chatProjectLink">Open Project</a>
                </div>
            </header>

            <div class="wa-chat-body" id="chatMessages">
                <p class="wa-system-note mb-0">No conversation selected yet.</p>
            </div>

            <footer class="wa-composer">
                <form class="wa-composer-form" id="chatComposerForm">
                    <input
                        id="chatComposerInput"
                        type="text"
                        class="form-control wa-input"
                        placeholder="Type a message"
                        maxlength="2000"
                        disabled
                    >
                    <button type="submit" class="wa-send-btn" id="chatComposerSubmit" disabled>
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
                <p class="wa-status" id="chatSystemStatus">Connecting to Firebase...</p>
            </footer>
        </section>
    </div>
</div>

@push('scripts')
<script type="module">
    import { getApp, getApps, initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js';
    import { getAuth, signInWithCustomToken } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js';
    import {
        addDoc,
        collection,
        doc,
        getFirestore,
        increment,
        onSnapshot,
        orderBy,
        query,
        serverTimestamp,
        setDoc,
    } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-firestore.js';

    const conversationContexts = @json($conversationContexts ?? []);
    const firebaseClientConfig = @json($firebaseClientConfig ?? []);
    const firebaseServerReady = @json($firebaseServerReady ?? false);
    const firebaseTokenEndpoint = @json($firebaseTokenEndpoint ?? '');
    const currentUserMeta = @json($currentUserMeta ?? []);

    const messagingShell = document.getElementById('waMessagingShell');
    const conversationListElement = document.getElementById('conversationList');
    const conversationCountBadge = document.getElementById('conversationCountBadge');
    const conversationSearchInput = document.getElementById('conversationSearchInput');
    const mobileBackButton = document.getElementById('mobileBackButton');
    const chatTitleElement = document.getElementById('chatTitle');
    const chatMetaElement = document.getElementById('chatMeta');
    const chatProjectLinkElement = document.getElementById('chatProjectLink');
    const chatMessagesElement = document.getElementById('chatMessages');
    const chatComposerForm = document.getElementById('chatComposerForm');
    const chatComposerInput = document.getElementById('chatComposerInput');
    const chatComposerSubmit = document.getElementById('chatComposerSubmit');
    const chatSystemStatus = document.getElementById('chatSystemStatus');

    const conversationStateMap = new Map();
    let activeConversationId = null;
    let conversationSearchQuery = '';
    let unsubscribeMessages = null;
    const unsubscribeConversationMeta = [];
    let db = null;
    let authUserUid = null;

    for (const context of conversationContexts) {
        conversationStateMap.set(context.conversation_id, {
            ...context,
            last_message_preview: '',
            last_message_at_ms: Number(context.sort_epoch || 0) * 1000,
            unread_count: 0,
        });
    }

    function currentUnreadField() {
        return currentUserMeta.role === 'Owner' ? 'unread_owner_count' : 'unread_contractor_count';
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll('\'', '&#039;');
    }

    function formatEpochMs(epochMs) {
        if (!epochMs || Number.isNaN(epochMs)) {
            return '';
        }

        const date = new Date(epochMs);
        return date.toLocaleString([], {
            day: '2-digit',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function initialsFromName(name) {
        const words = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (words.length === 0) {
            return 'U';
        }

        if (words.length === 1) {
            return words[0].slice(0, 2).toUpperCase();
        }

        return (words[0][0] + words[1][0]).toUpperCase();
    }

    function setSystemStatus(message, variant = 'muted') {
        chatSystemStatus.textContent = message;
        chatSystemStatus.classList.remove('text-danger', 'text-success');

        if (variant === 'error') {
            chatSystemStatus.classList.add('text-danger');
            return;
        }

        if (variant === 'success') {
            chatSystemStatus.classList.add('text-success');
        }
    }

    function normalizeFirebaseError(error, fallbackMessage) {
        const rawMessage = error instanceof Error ? error.message : String(fallbackMessage || 'Unexpected Firebase error.');
        const code = error && typeof error === 'object' && 'code' in error ? String(error.code || '') : '';

        if (code === 'permission-denied' || rawMessage.toLowerCase().includes('insufficient permissions')) {
            return 'Missing or insufficient permissions. Deploy Firestore rules and ensure valid bid or hire thread.';
        }

        if (code === 'unauthenticated') {
            return 'Firebase authentication failed. Refresh the page and sign in again.';
        }

        return rawMessage || fallbackMessage || 'Unexpected Firebase error.';
    }

    function sortedConversations() {
        return Array.from(conversationStateMap.values())
            .sort((left, right) => (right.last_message_at_ms || 0) - (left.last_message_at_ms || 0));
    }

    function filteredConversations() {
        const queryText = conversationSearchQuery.trim().toLowerCase();
        if (!queryText) {
            return sortedConversations();
        }

        return sortedConversations().filter((context) => {
            const haystack = [
                context.counterparty?.name,
                context.counterparty?.email,
                context.project?.title,
                context.project?.reference_code,
            ]
                .filter(Boolean)
                .join(' ')
                .toLowerCase();

            return haystack.includes(queryText);
        });
    }

    function renderConversationList() {
        const contexts = filteredConversations();
        const totalUnreadCount = contexts.reduce((sum, context) => sum + Number(context.unread_count || 0), 0);
        conversationCountBadge.textContent = String(totalUnreadCount || contexts.length);

        if (contexts.length === 0) {
            const message = conversationStateMap.size === 0
                ? 'No conversations available yet.'
                : 'No matches found for your search.';

            conversationListElement.innerHTML = `
                <div class="p-3">
                    <p class="mb-1 fw-semibold text-light">${escapeHtml(message)}</p>
                    <p class="wa-system-note mb-0">Conversations appear after bid or hire activity.</p>
                </div>
            `;
            return;
        }

        conversationListElement.innerHTML = contexts.map((context) => {
            const isActive = activeConversationId === context.conversation_id ? 'active' : '';
            const timeLabel = context.last_message_at_ms ? formatEpochMs(context.last_message_at_ms) : '';
            const preview = context.last_message_preview || 'No messages yet.';
            const relationshipText = context.relationship?.type === 'hire'
                ? `Hire: ${context.relationship?.status || 'active'}`
                : `Bid: ${context.relationship?.status || 'pending'}`;
            const counterpartyName = context.counterparty?.name || 'User';
            const counterpartyImageUrl = String(context.counterparty?.profile_image_url || '').trim();
            const unreadCount = Number(context.unread_count || 0);
            const avatarHtml = counterpartyImageUrl
                ? `<img src="${escapeHtml(counterpartyImageUrl)}" alt="${escapeHtml(counterpartyName)}" class="wa-avatar-image">`
                : `<span class="wa-avatar">${escapeHtml(initialsFromName(counterpartyName))}</span>`;
            const unreadBadge = unreadCount > 0
                ? `<span class="wa-unread-badge">${escapeHtml(unreadCount)}</span>`
                : '';

            return `
                <button type="button" class="wa-conversation-item ${isActive}" data-conversation-id="${escapeHtml(context.conversation_id)}">
                    ${avatarHtml}
                    <div class="wa-conv-main">
                        <div class="wa-conv-top">
                            <p class="wa-conv-name">${escapeHtml(counterpartyName)}</p>
                            <div class="d-inline-flex align-items-center gap-1">
                                <span class="wa-conv-time">${escapeHtml(timeLabel)}</span>
                                ${unreadBadge}
                            </div>
                        </div>
                        <p class="wa-conv-project">${escapeHtml(context.project?.reference_code || '')} | ${escapeHtml(context.project?.title || 'Project')}</p>
                        <p class="wa-conv-preview">${escapeHtml(preview)}</p>
                        <span class="wa-chip">${escapeHtml(relationshipText)}</span>
                    </div>
                </button>
            `;
        }).join('');

        conversationListElement.querySelectorAll('[data-conversation-id]').forEach((element) => {
            element.addEventListener('click', () => {
                const conversationId = element.getAttribute('data-conversation-id');
                if (!conversationId) {
                    return;
                }

                selectConversation(conversationId);
            });
        });
    }

    function renderMessages(messageDocs) {
        if (messageDocs.length === 0) {
            chatMessagesElement.innerHTML = '<p class="wa-system-note mb-0">No messages yet. Start this conversation.</p>';
            return;
        }

        const html = messageDocs.map((messageDoc) => {
            const messageData = messageDoc.data();
            const senderUid = String(messageData.sender_uid || '');
            const isMine = senderUid === authUserUid;
            const rowClass = isMine ? 'me' : 'other';
            const text = String(messageData.text || '');
            const createdAt = messageData.created_at?.toDate ? messageData.created_at.toDate() : null;
            const time = createdAt ? createdAt.toLocaleString([], {
                hour: '2-digit',
                minute: '2-digit',
            }) : '';
            const safeText = escapeHtml(text.trim());

            return `<div class="wa-message-row ${rowClass}"><div class="wa-bubble"><span class="wa-msg-text">${safeText}</span><span class="wa-time">${escapeHtml(time)}</span></div></div>`;
        }).join('');

        chatMessagesElement.innerHTML = html;
        chatMessagesElement.scrollTop = chatMessagesElement.scrollHeight;
    }

    function setChatHeader(context) {
        chatTitleElement.textContent = `${context.counterparty?.name || 'User'} (${context.counterparty?.role || ''})`;
        chatMetaElement.textContent = `${context.project?.reference_code || ''} | ${context.project?.title || 'Project'} | ${context.relationship?.type || 'bid'}`;

        if (context.project?.url) {
            chatProjectLinkElement.href = context.project.url;
            chatProjectLinkElement.classList.remove('d-none');
        } else {
            chatProjectLinkElement.classList.add('d-none');
        }
    }

    async function markConversationRead(context) {
        if (!db || !context) {
            return;
        }

        const conversationRef = doc(db, 'conversations', context.conversation_id);
        const unreadField = currentUnreadField();
        const readAtField = currentUserMeta.role === 'Owner' ? 'last_read_owner_at' : 'last_read_contractor_at';

        context.unread_count = 0;
        conversationStateMap.set(context.conversation_id, context);
        renderConversationList();

        await setDoc(conversationRef, {
            [unreadField]: 0,
            [readAtField]: serverTimestamp(),
        }, { merge: true });
    }

    function selectConversation(conversationId) {
        const context = conversationStateMap.get(conversationId);
        if (!context || !db) {
            return;
        }

        activeConversationId = conversationId;
        renderConversationList();
        setChatHeader(context);
        messagingShell.classList.add('wa-mobile-chat-active');
        markConversationRead(context).catch(() => {});

        if (unsubscribeMessages) {
            unsubscribeMessages();
            unsubscribeMessages = null;
        }

        const messageCollectionRef = collection(db, 'conversations', context.conversation_id, 'messages');
        const messageQuery = query(messageCollectionRef, orderBy('created_at', 'asc'));

        unsubscribeMessages = onSnapshot(messageQuery, (snapshot) => {
            renderMessages(snapshot.docs);
        }, (error) => {
            setSystemStatus(normalizeFirebaseError(error, 'Unable to load conversation messages.'), 'error');
        });

        chatComposerInput.disabled = false;
        chatComposerSubmit.disabled = false;
        chatComposerInput.focus();
    }

    function attachConversationMetadataListeners() {
        for (const context of conversationStateMap.values()) {
            const conversationRef = doc(db, 'conversations', context.conversation_id);
            const unsubscribe = onSnapshot(conversationRef, (snapshot) => {
                if (!snapshot.exists()) {
                    return;
                }

                const data = snapshot.data();
                const current = conversationStateMap.get(context.conversation_id);
                if (!current) {
                    return;
                }

                const lastAt = data.last_message_at?.toDate ? data.last_message_at.toDate().getTime() : current.last_message_at_ms;
                current.last_message_preview = String(data.last_message_preview || current.last_message_preview || '');
                current.last_message_at_ms = lastAt || current.last_message_at_ms;
                current.unread_count = Number(data[currentUnreadField()] || 0);
                conversationStateMap.set(context.conversation_id, current);
                renderConversationList();
            }, (error) => {
                setSystemStatus(normalizeFirebaseError(error, 'Unable to sync conversations.'), 'error');
            });

            unsubscribeConversationMeta.push(unsubscribe);
        }
    }

    async function sendActiveConversationMessage(messageText) {
        if (!activeConversationId || !db) {
            return;
        }

        const context = conversationStateMap.get(activeConversationId);
        if (!context) {
            return;
        }

        const conversationRef = doc(db, 'conversations', context.conversation_id);
        const participants = [context.participants.owner_uid, context.participants.contractor_uid];

        await setDoc(conversationRef, {
            conversation_id: context.conversation_id,
            project_id: context.project.id,
            project_title: context.project.title,
            project_reference_code: context.project.reference_code,
            project_status: context.project.status,
            owner_user_id: context.participants.owner_user_id,
            contractor_user_id: context.participants.contractor_user_id,
            participants,
            relationship_type: context.relationship.type,
            relationship_status: context.relationship.status,
            last_message_preview: messageText.substring(0, 160),
            last_message_sender_uid: authUserUid,
            last_message_at: serverTimestamp(),
            unread_owner_count: currentUserMeta.role === 'Owner' ? 0 : increment(1),
            unread_contractor_count: currentUserMeta.role === 'Contractor' ? 0 : increment(1),
            updated_at: serverTimestamp(),
        }, { merge: true });

        await addDoc(collection(conversationRef, 'messages'), {
            text: messageText,
            sender_uid: authUserUid,
            sender_user_id: currentUserMeta.user_id,
            sender_name: currentUserMeta.name,
            sender_role: currentUserMeta.role,
            created_at: serverTimestamp(),
        });
    }

    async function bootMessaging() {
        if (!firebaseServerReady) {
            setSystemStatus('Firebase token service is not configured. Add backend Firebase credentials in .env.', 'error');
            conversationListElement.innerHTML = '<div class="p-3"><p class="mb-0 text-danger">Firebase backend is not configured.</p></div>';
            return;
        }

        const requiredClientKeys = ['apiKey', 'authDomain', 'projectId', 'appId'];
        const missingClientKeys = requiredClientKeys.filter((key) => !firebaseClientConfig[key]);

        if (missingClientKeys.length > 0) {
            setSystemStatus(`Firebase client config missing: ${missingClientKeys.join(', ')}`, 'error');
            conversationListElement.innerHTML = '<div class="p-3"><p class="mb-0 text-danger">Firebase client config is incomplete.</p></div>';
            return;
        }

        renderConversationList();

        if (conversationStateMap.size === 0) {
            setSystemStatus('No eligible conversations yet. Bids and hires will appear automatically.', 'muted');
            return;
        }

        try {
            const response = await fetch(firebaseTokenEndpoint, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            const payload = await response.json();
            if (!response.ok || !payload.token) {
                throw new Error(payload.message || 'Unable to get Firebase auth token.');
            }

            const app = getApps().length ? getApp() : initializeApp(firebaseClientConfig);
            const auth = getAuth(app);
            await signInWithCustomToken(auth, payload.token);

            authUserUid = String(payload.uid || currentUserMeta.firebase_uid || '');
            db = getFirestore(app);

            attachConversationMetadataListeners();
            setSystemStatus('Connected. Real-time chat is active.', 'success');

            const firstConversation = filteredConversations()[0];
            if (firstConversation) {
                selectConversation(firstConversation.conversation_id);
            }
        } catch (error) {
            setSystemStatus(normalizeFirebaseError(error, 'Failed to initialize Firebase chat.'), 'error');
        }
    }

    conversationSearchInput.addEventListener('input', () => {
        conversationSearchQuery = conversationSearchInput.value || '';
        renderConversationList();
    });

    if (mobileBackButton) {
        mobileBackButton.addEventListener('click', () => {
            messagingShell.classList.remove('wa-mobile-chat-active');
        });
    }

    chatComposerForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const rawMessage = chatComposerInput.value.trim();
        if (!rawMessage || !db) {
            return;
        }

        chatComposerSubmit.disabled = true;

        try {
            await sendActiveConversationMessage(rawMessage);
            chatComposerInput.value = '';
            setSystemStatus('Message sent.', 'success');
        } catch (error) {
            setSystemStatus(normalizeFirebaseError(error, 'Failed to send message.'), 'error');
        } finally {
            chatComposerSubmit.disabled = false;
            chatComposerInput.focus();
        }
    });

    window.addEventListener('beforeunload', () => {
        if (unsubscribeMessages) {
            unsubscribeMessages();
        }

        for (const unsubscribe of unsubscribeConversationMeta) {
            unsubscribe();
        }
    });

    bootMessaging();
</script>
@endpush
