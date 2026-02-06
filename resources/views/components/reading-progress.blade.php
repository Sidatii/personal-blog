<div x-data="{ progress: 0 }" x-init="calculate()" @scroll.window="calculate()"
     class="fixed top-0 left-0 w-full h-1 z-50">
    {{-- Background bar --}}
    <div class="w-full h-full bg-rose-pine-surface"></div>
    
    {{-- Progress bar --}}
    <div class="h-full bg-rose-pine-pine transition-all duration-150 absolute top-0 left-0"
         :style="`width: ${progress}%`"></div>
</div>

<script>
function calculate() {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    this.progress = Math.min(100, Math.max(0, (scrollTop / docHeight) * 100));
}
</script>
