    </main>
    <?php if(isset($_SESSION['usuario_id'])): ?>
    <!-- BottomNavBar -->
    <nav class="bg-surface-container-highest border-t border-outline-variant shadow-lg fixed bottom-0 w-full z-50 flex justify-around items-center h-20 pb-safe px-2">
        <a class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1 hover:bg-secondary-container transition-all active:scale-90 duration-150" href="/tutor/index.php">
            <img src="/tutor/assets/imgs/logo_learning.png" alt="Logo" class="h-6 object-contain mb-1 opacity-80 group-hover:opacity-100">
            <span class="font-label-md text-label-md mt-1">Inicio</span>
        </a>
        <a class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1 hover:bg-secondary-container transition-all active:scale-90 duration-150" href="/tutor/views/lista_herramientas.php">
            <span class="material-symbols-outlined">architecture</span>
            <span class="font-label-md text-label-md mt-1">Tutoriales</span>
        </a>
        <a class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1 hover:bg-secondary-container transition-all active:scale-90 duration-150" href="/tutor/views/asistente_ia.php">
            <span class="material-symbols-outlined">psychology</span>
            <span class="font-label-md text-label-md mt-1">IA Assistant</span>
        </a>
        <a class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1 hover:bg-secondary-container transition-all active:scale-90 duration-150" href="/tutor/estudiante/dashboard.php">
            <span class="material-symbols-outlined">star</span>
            <span class="font-label-md text-label-md mt-1">Mi Progreso</span>
        </a>
    </nav>
    <?php endif; ?>
</body>
</html>
