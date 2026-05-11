<template>
    <footer
        class="bg-background bg-gray-200 dark:bg-gray-800 border-t bo text-foreground-700 ">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row justify-between items-center">

            <div class="text-sm text-gray-800 dark:text-gray-200">
                &copy; {{ new Date().getFullYear() }} KizunaSound. Copyright.
            </div>

            <div class="mt-2 sm:mt-0 flex gap-4">

                <a href="https://bjdevstudio.my.canva.site/" class="hover:text-primary dark:hover:text-primary transition-colors text-sm text-gray-800 dark:text-gray-200">Help</a>
            </div>
        </div>
    </footer>
</template>

<script setup>
</script>
