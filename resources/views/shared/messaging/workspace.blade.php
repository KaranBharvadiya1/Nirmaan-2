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
        flex-direction: column;
        align-items: stretch;
        gap: 0.38rem;
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
        display: block;
        white-space: pre-wrap;
        word-break: break-word;
        line-height: 1.22;
        margin: 0;
    }

    .wa-message-footer {
        display: flex;
        justify-content: flex-end;
    }

    .wa-message-attachments {
        display: grid;
        gap: 0.4rem;
    }

    .wa-message-attachment-card {
        display: flex;
        flex-direction: column;
        gap: 0.28rem;
    }

    .wa-message-attachment-link {
        display: block;
        text-decoration: none;
    }

    .wa-message-image,
    .wa-message-video {
        display: block;
        width: min(100%, 260px);
        max-height: 260px;
        border-radius: 0.8rem;
        background: #dfe7f7;
        object-fit: cover;
    }

    .wa-message-video {
        object-fit: contain;
    }

    .wa-message-attachment-name {
        font-size: 0.72rem;
        color: #5d6a85;
        text-decoration: none;
        word-break: break-word;
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
        align-items: flex-end;
    }

    .wa-input-wrap {
        flex: 1;
        min-width: 0;
    }

    .wa-composer-row {
        display: flex;
        gap: 0.55rem;
        align-items: center;
    }

    .wa-attach-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid #c7d5f4;
        background: #fff;
        color: #2452e6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .wa-attach-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .wa-attachment-preview-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        margin-bottom: 0.55rem;
    }

    .wa-attachment-preview-card {
        width: 86px;
        border-radius: 0.9rem;
        background: #fff;
        border: 1px solid #d7e3fb;
        box-shadow: 0 8px 18px rgba(36, 82, 230, 0.08);
        overflow: hidden;
        position: relative;
    }

    .wa-attachment-preview-thumb {
        width: 100%;
        height: 68px;
        object-fit: cover;
        display: block;
        background: #e9eef9;
    }

    .wa-attachment-preview-meta {
        padding: 0.35rem 0.45rem 0.4rem;
    }

    .wa-attachment-preview-name {
        margin: 0;
        font-size: 0.68rem;
        font-weight: 600;
        color: #203355;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .wa-attachment-preview-size {
        margin: 0.12rem 0 0;
        font-size: 0.62rem;
        color: #63708c;
    }

    .wa-attachment-remove {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 0;
        background: rgba(15, 23, 42, 0.72);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
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
                        id="chatAttachmentInput"
                        type="file"
                        class="d-none"
                        accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime"
                        multiple
                        disabled
                    >
                    <div class="wa-input-wrap">
                        <div class="wa-attachment-preview-list d-none" id="chatAttachmentPreviewList"></div>
                        <div class="wa-composer-row">
                            <button type="button" class="wa-attach-btn" id="chatAttachmentButton" disabled aria-label="Add images or videos">
                                <i class="bi bi-paperclip"></i>
                            </button>
                            <input
                                id="chatComposerInput"
                                type="text"
                                class="form-control wa-input"
                                placeholder="Type a message or send media"
                                maxlength="2000"
                                disabled
                            >
                        </div>
                    </div>
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
    const chatAttachmentUploadEndpoint = @json($chatAttachmentUploadEndpoint ?? '');
    const currentUserMeta = @json($currentUserMeta ?? []);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

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
    const chatAttachmentInput = document.getElementById('chatAttachmentInput');
    const chatAttachmentButton = document.getElementById('chatAttachmentButton');
    const chatAttachmentPreviewList = document.getElementById('chatAttachmentPreviewList');
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
    let pendingAttachmentFiles = [];
    let pendingConversationId = null;
    let firebaseBootFailed = false;

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

    function formatFileSize(fileSize) {
        const size = Number(fileSize || 0);

        if (size <= 0) {
            return '';
        }

        if (size < 1024 * 1024) {
            return `${(size / 1024).toFixed(1)} KB`;
        }

        return `${(size / (1024 * 1024)).toFixed(1)} MB`;
    }

    function makeAttachmentPreviewId() {
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }

        return `${Date.now()}-${Math.random().toString(16).slice(2)}`;
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

        if (code === 'auth/unauthorized-domain' || rawMessage.toLowerCase().includes('unauthorized-domain')) {
            return `Firebase blocked ${window.location.hostname}. Add this domain in Firebase Authentication > Settings > Authorized domains, then reload.`;
        }

        return rawMessage || fallbackMessage || 'Unexpected Firebase error.';
    }

    function attachmentPreviewLabel(attachments) {
        if (!Array.isArray(attachments) || attachments.length === 0) {
            return '';
        }

        if (attachments.length === 1) {
            return attachments[0]?.media_type === 'video' ? '[Video]' : '[Image]';
        }

        return `[${attachments.length} attachments]`;
    }

    function buildMessagePreview(messageText, attachments = []) {
        const trimmedMessage = String(messageText || '').trim();
        const attachmentLabel = attachmentPreviewLabel(attachments);

        if (trimmedMessage !== '' && attachmentLabel !== '') {
            return `${trimmedMessage} ${attachmentLabel}`.trim().substring(0, 160);
        }

        if (trimmedMessage !== '') {
            return trimmedMessage.substring(0, 160);
        }

        return attachmentLabel || 'New message';
    }

    function clearPendingAttachments() {
        pendingAttachmentFiles.forEach((attachment) => {
            if (attachment.previewUrl) {
                URL.revokeObjectURL(attachment.previewUrl);
            }
        });

        pendingAttachmentFiles = [];
        chatAttachmentInput.value = '';
        renderPendingAttachmentPreviews();
    }

    function renderPendingAttachmentPreviews() {
        if (!pendingAttachmentFiles.length) {
            chatAttachmentPreviewList.innerHTML = '';
            chatAttachmentPreviewList.classList.add('d-none');
            return;
        }

        chatAttachmentPreviewList.classList.remove('d-none');
        chatAttachmentPreviewList.innerHTML = pendingAttachmentFiles.map((attachment) => {
            const safeName = escapeHtml(attachment.file.name || 'Attachment');
            const safeSize = escapeHtml(formatFileSize(attachment.file.size));
            const previewContent = attachment.type === 'video'
                ? `<video class="wa-attachment-preview-thumb" muted playsinline preload="metadata"><source src="${escapeHtml(attachment.previewUrl)}" type="${escapeHtml(attachment.file.type || 'video/mp4')}"></video>`
                : `<img src="${escapeHtml(attachment.previewUrl)}" alt="${safeName}" class="wa-attachment-preview-thumb">`;

            return `
                <div class="wa-attachment-preview-card">
                    <button type="button" class="wa-attachment-remove" data-remove-attachment="${escapeHtml(attachment.id)}" aria-label="Remove attachment">
                        <i class="bi bi-x"></i>
                    </button>
                    ${previewContent}
                    <div class="wa-attachment-preview-meta">
                        <p class="wa-attachment-preview-name">${safeName}</p>
                        <p class="wa-attachment-preview-size">${safeSize}</p>
                    </div>
                </div>
            `;
        }).join('');

        chatAttachmentPreviewList.querySelectorAll('[data-remove-attachment]').forEach((button) => {
            button.addEventListener('click', () => {
                const attachmentId = button.getAttribute('data-remove-attachment');
                if (!attachmentId) {
                    return;
                }

                const attachmentToRemove = pendingAttachmentFiles.find((attachment) => attachment.id === attachmentId);
                if (attachmentToRemove?.previewUrl) {
                    URL.revokeObjectURL(attachmentToRemove.previewUrl);
                }

                pendingAttachmentFiles = pendingAttachmentFiles.filter((attachment) => attachment.id !== attachmentId);
                renderPendingAttachmentPreviews();
            });
        });
    }

    function renderMessageAttachment(attachment) {
        const url = String(attachment?.url || '').trim();
        if (url === '') {
            return '';
        }

        const mediaType = String(attachment?.media_type || 'image');
        const originalName = String(attachment?.original_name || (mediaType === 'video' ? 'Video' : 'Image'));
        const safeUrl = escapeHtml(url);
        const safeName = escapeHtml(originalName);
        const safeMimeType = escapeHtml(String(attachment?.mime_type || (mediaType === 'video' ? 'video/mp4' : 'image/jpeg')));

        if (mediaType === 'video') {
            return `
                <div class="wa-message-attachment-card">
                    <video class="wa-message-video" controls preload="metadata" playsinline>
                        <source src="${safeUrl}" type="${safeMimeType}">
                    </video>
                    <a href="${safeUrl}" target="_blank" rel="noopener noreferrer" class="wa-message-attachment-name">${safeName}</a>
                </div>
            `;
        }

        return `
            <div class="wa-message-attachment-card">
                <a href="${safeUrl}" target="_blank" rel="noopener noreferrer" class="wa-message-attachment-link">
                    <img src="${safeUrl}" alt="${safeName}" class="wa-message-image">
                </a>
                <a href="${safeUrl}" target="_blank" rel="noopener noreferrer" class="wa-message-attachment-name">${safeName}</a>
            </div>
        `;
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
            const attachments = Array.isArray(messageData.attachments) ? messageData.attachments : [];
            const createdAt = messageData.created_at?.toDate ? messageData.created_at.toDate() : null;
            const time = createdAt ? createdAt.toLocaleString([], {
                hour: '2-digit',
                minute: '2-digit',
            }) : '';
            const safeText = escapeHtml(text.trim());
            const attachmentsHtml = attachments.length > 0
                ? `<div class="wa-message-attachments">${attachments.map((attachment) => renderMessageAttachment(attachment)).join('')}</div>`
                : '';
            const textHtml = safeText !== '' ? `<p class="wa-msg-text">${safeText}</p>` : '';

            return `
                <div class="wa-message-row ${rowClass}">
                    <div class="wa-bubble">
                        ${attachmentsHtml}
                        ${textHtml}
                        <div class="wa-message-footer">
                            <span class="wa-time">${escapeHtml(time)}</span>
                        </div>
                    </div>
                </div>
            `;
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
        if (!context) {
            return;
        }

        if (activeConversationId !== conversationId) {
            clearPendingAttachments();
        }

        activeConversationId = conversationId;
        pendingConversationId = conversationId;
        renderConversationList();
        setChatHeader(context);
        messagingShell.classList.add('wa-mobile-chat-active');

        if (!db) {
            setSystemStatus(
                firebaseBootFailed
                    ? `Realtime chat is unavailable on ${window.location.hostname} until Firebase connects.`
                    : 'Connecting to realtime chat. Please wait a moment.',
                firebaseBootFailed ? 'error' : 'muted',
            );

            return;
        }

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
        chatAttachmentInput.disabled = false;
        chatAttachmentButton.disabled = false;
        chatComposerSubmit.disabled = false;
        pendingConversationId = null;
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

    async function uploadChatAttachments(conversationId, attachments) {
        if (!chatAttachmentUploadEndpoint) {
            throw new Error('Attachment uploads are not configured.');
        }

        const formData = new FormData();
        formData.append('conversation_id', conversationId);

        attachments.forEach((attachment) => {
            formData.append('attachments[]', attachment.file, attachment.file.name);
        });

        const response = await fetch(chatAttachmentUploadEndpoint, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
            credentials: 'same-origin',
        });

        const payload = await response.json().catch(() => ({}));
        if (!response.ok || !Array.isArray(payload.attachments)) {
            const validationMessage = payload?.errors
                ? Object.values(payload.errors).flat().join(' ')
                : null;

            throw new Error(validationMessage || payload.message || 'Failed to upload attachments.');
        }

        return payload.attachments;
    }

    async function sendActiveConversationMessage(messageText, attachments = []) {
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
            last_message_preview: buildMessagePreview(messageText, attachments),
            last_message_sender_uid: authUserUid,
            last_message_at: serverTimestamp(),
            unread_owner_count: currentUserMeta.role === 'Owner' ? 0 : increment(1),
            unread_contractor_count: currentUserMeta.role === 'Contractor' ? 0 : increment(1),
            updated_at: serverTimestamp(),
        }, { merge: true });

        await addDoc(collection(conversationRef, 'messages'), {
            text: messageText,
            attachments,
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
            firebaseBootFailed = false;

            attachConversationMetadataListeners();
            setSystemStatus('Connected. Real-time chat is active.', 'success');

            const preferredConversationId = pendingConversationId || filteredConversations()[0]?.conversation_id || null;
            if (preferredConversationId) {
                selectConversation(preferredConversationId);
            }
        } catch (error) {
            firebaseBootFailed = true;
            setSystemStatus(normalizeFirebaseError(error, 'Failed to initialize Firebase chat.'), 'error');
        }
    }

    conversationSearchInput.addEventListener('input', () => {
        conversationSearchQuery = conversationSearchInput.value || '';
        renderConversationList();
    });

    if (chatAttachmentButton) {
        chatAttachmentButton.addEventListener('click', () => {
            if (chatAttachmentButton.disabled) {
                return;
            }

            chatAttachmentInput.click();
        });
    }

    if (chatAttachmentInput) {
        chatAttachmentInput.addEventListener('change', () => {
            const selectedFiles = Array.from(chatAttachmentInput.files || []);
            if (!selectedFiles.length) {
                return;
            }

            for (const file of selectedFiles) {
                if (pendingAttachmentFiles.length >= 5) {
                    setSystemStatus('You can attach up to 5 files in one message.', 'error');
                    break;
                }

                if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
                    setSystemStatus('Only image and video files are allowed.', 'error');
                    continue;
                }

                if (file.size > 25 * 1024 * 1024) {
                    setSystemStatus('Each attachment must be 25 MB or smaller.', 'error');
                    continue;
                }

                pendingAttachmentFiles.push({
                    id: makeAttachmentPreviewId(),
                    file,
                    type: file.type.startsWith('video/') ? 'video' : 'image',
                    previewUrl: URL.createObjectURL(file),
                });
            }

            chatAttachmentInput.value = '';
            renderPendingAttachmentPreviews();
        });
    }

    if (mobileBackButton) {
        mobileBackButton.addEventListener('click', () => {
            messagingShell.classList.remove('wa-mobile-chat-active');
        });
    }

    chatComposerForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const rawMessage = chatComposerInput.value.trim();
        if ((!rawMessage && pendingAttachmentFiles.length === 0) || !db || !activeConversationId) {
            return;
        }

        const attachmentsToUpload = [...pendingAttachmentFiles];
        chatComposerSubmit.disabled = true;
        chatComposerInput.disabled = true;
        chatAttachmentButton.disabled = true;
        chatAttachmentInput.disabled = true;

        try {
            const uploadedAttachments = attachmentsToUpload.length > 0
                ? await uploadChatAttachments(activeConversationId, attachmentsToUpload)
                : [];

            await sendActiveConversationMessage(rawMessage, uploadedAttachments);
            chatComposerInput.value = '';
            clearPendingAttachments();
            setSystemStatus('Message sent.', 'success');
        } catch (error) {
            setSystemStatus(normalizeFirebaseError(error, 'Failed to send message.'), 'error');
        } finally {
            const composerEnabled = Boolean(activeConversationId);
            chatComposerInput.disabled = !composerEnabled;
            chatComposerSubmit.disabled = !composerEnabled;
            chatAttachmentButton.disabled = !composerEnabled;
            chatAttachmentInput.disabled = !composerEnabled;
            if (composerEnabled) {
                chatComposerInput.focus();
            }
        }
    });

    window.addEventListener('beforeunload', () => {
        clearPendingAttachments();

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
