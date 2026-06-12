<?php
session_start();
require_once '../config/database.php';

$page_title = "Asistente de Diseño IA";
$header_title = "AI Design Mentor";

include '../includes/header.php';
?>

<div class="flex flex-col h-[calc(100vh-160px)]">
    <!-- Chat Container -->
    <div id="chat-container" class="flex-grow overflow-y-auto space-y-md p-base custom-scrollbar bg-surface-container-low rounded-xl border border-outline-variant mb-md">
        <!-- AI Welcome Message -->
        <div class="flex justify-start">
            <div class="bg-surface-container-highest p-md rounded-2xl rounded-tl-none max-w-[85%] border border-outline-variant">
                <p class="font-body-md text-on-surface">¡Hola! Soy tu asistente experto en Adobe Creative Suite. ¿En qué puedo ayudarte hoy con Photoshop, Illustrator o InDesign?</p>
                <span class="text-[10px] text-on-surface-variant mt-1 block">AI Mentor • Ahora</span>
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="relative">
        <form id="ai-chat-form" class="flex gap-sm items-center">
            <input type="text" id="user-input" placeholder="Pregunta sobre una herramienta..." 
                class="flex-grow bg-surface-container-highest text-on-surface border border-outline-variant rounded-full px-lg py-3 focus:outline-none focus:border-primary-container transition-all">
            <button type="submit" id="send-btn" class="bg-primary-container text-on-primary p-3 rounded-full hover:brightness-110 active:scale-90 transition-all">
                <span class="material-symbols-outlined">send</span>
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('ai-chat-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const input = document.getElementById('user-input');
    const message = input.value.trim();
    if (!message) return;

    appendMessage('user', message);
    input.value = '';
    
    // Typing indicator
    const typingId = 'typing-' + Date.now();
    appendTypingIndicator(typingId);

    try {
        const response = await fetch('/services/ai_service.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(message)
        });
        const data = await response.json();
        
        removeTypingIndicator(typingId);
        if (data.success) {
            appendMessage('ai', data.reply);
        } else {
            appendMessage('ai', 'Lo siento, hubo un error al procesar tu consulta: ' + data.error);
        }
    } catch (error) {
        removeTypingIndicator(typingId);
        appendMessage('ai', 'Error de conexión con el servidor.');
    }
});

function appendMessage(sender, text) {
    const container = document.getElementById('chat-container');
    const div = document.createElement('div');
    div.className = sender === 'user' ? 'flex justify-end' : 'flex justify-start';
    
    const bubbleClass = sender === 'user' 
        ? 'bg-primary-container text-on-primary p-md rounded-2xl rounded-tr-none' 
        : 'bg-surface-container-highest text-on-surface p-md rounded-2xl rounded-tl-none border border-outline-variant';
    
    div.innerHTML = `
        <div class="${bubbleClass} max-w-[85%]">
            <p class="font-body-md">${text}</p>
            <span class="text-[10px] ${sender === 'user' ? 'text-on-primary/70' : 'text-on-surface-variant'} mt-1 block">
                ${sender === 'user' ? 'Tú' : 'AI Mentor'} • ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
            </span>
        </div>
    `;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function appendTypingIndicator(id) {
    const container = document.getElementById('chat-container');
    const div = document.createElement('div');
    div.id = id;
    div.className = 'flex justify-start';
    div.innerHTML = `
        <div class="bg-surface-container-highest p-md rounded-2xl rounded-tl-none border border-outline-variant">
            <div class="flex gap-1">
                <div class="w-1.5 h-1.5 bg-on-surface-variant rounded-full animate-bounce"></div>
                <div class="w-1.5 h-1.5 bg-on-surface-variant rounded-full animate-bounce [animation-delay:0.2s]"></div>
                <div class="w-1.5 h-1.5 bg-on-surface-variant rounded-full animate-bounce [animation-delay:0.4s]"></div>
            </div>
        </div>
    `;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function removeTypingIndicator(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
}
</script>

<?php include '../includes/footer.php'; ?>
