{{-- Custom Logo Loader Component --}}
{{-- Usage: @include('components.logo-loader', ['id' => 'page-loader', 'text' => 'Loading the !Trybe Community...']) --}}

@props(['id' => 'trybe-loader', 'text' => 'Loading the !Trybe Community...', 'show' => true])

<div id="{{ $id }}"
     x-data="{
        show: {{ $show ? 'true' : 'false' }},
        text: '{{ $text }}',
        displayedText: '',
        typingIndex: 0,
        typingComplete: false,

        init() {
            if (this.show) {
                this.startTyping();
            }
        },

        startTyping() {
            this.displayedText = '';
            this.typingIndex = 0;
            this.typingComplete = false;
            this.typeNextChar();
        },

        typeNextChar() {
            if (this.typingIndex < this.text.length) {
                this.displayedText += this.text[this.typingIndex];
                this.typingIndex++;
                setTimeout(() => this.typeNextChar(), 50);
            } else {
                this.typingComplete = true;
                // Restart typing after a pause
                setTimeout(() => this.startTyping(), 2000);
            }
        },

        hide() {
            this.show = false;
        }
     }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="loader-overlay"
     :class="{ 'hidden': !show }">

    <div class="loader-content">
        {{-- Logo with Heartbeat Animation --}}
        <div class="loader-logo-container">
            <img src="{{ asset('ui/logo/9yt-trybe-logo-dark.png') }}"
                 alt="9yt !Trybe"
                 class="loader-logo dark:block hidden">
            <img src="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}"
                 alt="9yt !Trybe"
                 class="loader-logo dark:hidden block">

            {{-- Animated Ring Around Logo --}}
            <div class="loader-ring"></div>
            <div class="loader-ring-2"></div>
        </div>

        {{-- Typing Text Animation --}}
        <div class="loader-text-container">
            <p class="loader-text">
                <span x-text="displayedText"></span>
                <span class="typing-cursor" :class="{ 'blink': !typingComplete }">|</span>
            </p>
        </div>

        {{-- Loading Dots --}}
        <div class="loader-dots">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>
</div>

<style>
/* Loader Overlay - LIQUID GLASS BLUR BACKGROUND */
.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(240, 248, 255, 0.12) 100%);
    backdrop-filter: blur(40px) saturate(200%) brightness(110%);
    -webkit-backdrop-filter: blur(40px) saturate(200%) brightness(110%);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
}

.dark .loader-overlay {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.25) 0%, rgba(30, 41, 59, 0.2) 100%);
    backdrop-filter: blur(40px) saturate(200%) brightness(90%);
    -webkit-backdrop-filter: blur(40px) saturate(200%) brightness(90%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.loader-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

/* Logo Container with Heartbeat - 2CM x 2CM on ALL LAYOUTS */
.loader-logo-container {
    position: relative;
    width: 2cm;
    height: 2cm;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    box-shadow: none;
}

.loader-logo {
    width: 1.5cm;
    height: auto;
    animation: heartbeat 1.5s ease-in-out infinite;
    filter: drop-shadow(0 4px 20px rgba(6, 182, 212, 0.4));
    background: transparent;
    border: none;
    outline: none;
    box-shadow: none;
}

/* Heartbeat Animation - Mini Zoom Continuous */
@keyframes heartbeat {
    0%, 100% {
        transform: scale(1);
    }
    15% {
        transform: scale(1.15);
    }
    30% {
        transform: scale(1);
    }
    45% {
        transform: scale(1.1);
    }
    60% {
        transform: scale(1);
    }
}

/* Animated Ring Around Logo - SIZED TO 2CM */
.loader-ring {
    position: absolute;
    width: 2cm;
    height: 2cm;
    border: 2px solid transparent;
    border-top-color: #0891b2;
    border-right-color: #06b6d4;
    border-radius: 50%;
    animation: ring-spin 1.5s linear infinite;
}

.loader-ring-2 {
    position: absolute;
    width: 1.7cm;
    height: 1.7cm;
    border: 2px solid transparent;
    border-bottom-color: #7c3aed;
    border-left-color: #a855f7;
    border-radius: 50%;
    animation: ring-spin-reverse 2s linear infinite;
}

@keyframes ring-spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

@keyframes ring-spin-reverse {
    0% {
        transform: rotate(360deg);
    }
    100% {
        transform: rotate(0deg);
    }
}

/* Typing Text */
.loader-text-container {
    text-align: center;
    min-height: 2rem;
}

.loader-text {
    font-size: 1.125rem;
    font-weight: 600;
    background: linear-gradient(135deg, #0891b2, #06b6d4, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.dark .loader-text {
    background: linear-gradient(135deg, #22d3ee, #06b6d4, #a855f7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Typing Cursor */
.typing-cursor {
    display: inline-block;
    margin-left: 2px;
    font-weight: normal;
    background: linear-gradient(135deg, #0891b2, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.typing-cursor.blink {
    animation: cursor-blink 0.7s step-end infinite;
}

@keyframes cursor-blink {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
}

/* Loading Dots */
.loader-dots {
    display: flex;
    gap: 0.5rem;
}

.loader-dots .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0891b2, #06b6d4);
    animation: dot-bounce 1.4s ease-in-out infinite;
}

.loader-dots .dot:nth-child(1) {
    animation-delay: 0s;
}

.loader-dots .dot:nth-child(2) {
    animation-delay: 0.2s;
}

.loader-dots .dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes dot-bounce {
    0%, 80%, 100% {
        transform: scale(0.6);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Responsive - MAINTAIN 2CM SIZE ON ALL DEVICES */
@media (max-width: 640px) {
    .loader-logo-container {
        width: 2cm;
        height: 2cm;
    }

    .loader-logo {
        width: 1.5cm;
    }

    .loader-text {
        font-size: 0.75rem;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .loader-logo {
        animation: none;
    }

    .loader-ring,
    .loader-ring-2 {
        animation: none;
        border-color: #0891b2;
    }

    .loader-dots .dot {
        animation: none;
    }

    .typing-cursor.blink {
        animation: none;
    }
}
</style>
