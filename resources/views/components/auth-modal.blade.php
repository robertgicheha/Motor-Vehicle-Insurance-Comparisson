<div class="auth-modal-overlay" id="authModal" style="display:none" x-data="{ tab: 'login', email: '', password: '', password_confirmation: '', loading: false, error: '', redirectTo: sessionStorage.getItem('auth_redirect') || '{{ route('dashboard') }}' }">
  <div class="auth-modal-split" @click.stop>
    <button class="auth-modal-close" onclick="closeAuthModal()">&times;</button>

    <!-- Left: Forms -->
    <div class="auth-modal-form-side">
      <div class="auth-modal-form-wrap">
        <div class="auth-tabs">
          <button class="auth-tab" :class="{ active: tab === 'login' }" @click="tab = 'login'">Login</button>
          <button class="auth-tab" :class="{ active: tab === 'register' }" @click="tab = 'register'">Sign Up</button>
        </div>

        <!-- Login Form -->
        <form x-show="tab === 'login'" method="POST" action="{{ route('login') }}" @submit="loading = true">
          @csrf
          <input type="hidden" name="redirect_to" :value="redirectTo">
          <div class="auth-field">
            <label for="auth-email">Email</label>
            <input type="email" id="auth-email" name="email" x-model="email" required placeholder="email@example.com">
          </div>
          <div class="auth-field">
            <label for="auth-password">Password</label>
            <input type="password" id="auth-password" name="password" x-model="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;">
          </div>
          <button type="submit" class="btn btn-primary auth-submit" :disabled="loading">Login</button>

          <div class="relative my-5">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
            <div class="relative flex justify-center text-sm"><span class="px-2 bg-white text-gray-400">or continue with</span></div>
          </div>

          <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-md hover:bg-gray-50 transition no-underline">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
              <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
              <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
              <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
              <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Sign in with Google
          </a>

          <p class="auth-switch">Don't have an account? <a href="#" @click.prevent="tab = 'register'">Sign Up</a></p>
        </form>

        <!-- Register Form -->
        <form x-show="tab === 'register'" method="POST" action="{{ route('register') }}" @submit="loading = true">
          @csrf
          <input type="hidden" name="redirect_to" :value="redirectTo">
          <div class="auth-grid">
            <div class="auth-field">
              <label for="auth-first">First Name</label>
              <input type="text" id="auth-first" name="first_name" required placeholder="John">
            </div>
            <div class="auth-field">
              <label for="auth-last">Last Name</label>
              <input type="text" id="auth-last" name="last_name" required placeholder="Doe">
            </div>
          </div>
          <div class="auth-field">
            <label for="auth-mobile">Phone Number</label>
            <input type="tel" id="auth-mobile" name="mobile" required placeholder="07 XX XXX XXX">
          </div>
          <div class="auth-field">
            <label for="auth-reg-email">Email</label>
            <input type="email" id="auth-reg-email" name="email" x-model="email" required placeholder="email@example.com">
          </div>
          <div class="auth-field">
            <label for="auth-reg-password">Password</label>
            <input type="password" id="auth-reg-password" name="password" x-model="password" required placeholder="Min. 8 characters" minlength="8">
          </div>
          <div class="auth-field">
            <label for="auth-reg-password-confirm">Confirm Password</label>
            <input type="password" id="auth-reg-password-confirm" name="password_confirmation" x-model="password_confirmation" required placeholder="Repeat password">
          </div>
          <button type="submit" class="btn btn-primary auth-submit" :disabled="loading">Create Account</button>

          <div class="relative my-5">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
            <div class="relative flex justify-center text-sm"><span class="px-2 bg-white text-gray-400">or sign up with</span></div>
          </div>

          <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-md hover:bg-gray-50 transition no-underline">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
              <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
              <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
              <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
              <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Sign up with Google
          </a>

          <p class="auth-switch">Already have an account? <a href="#" @click.prevent="tab = 'login'">Login</a></p>
        </form>
      </div>
    </div>

    <!-- Right: Image -->
    <div class="auth-modal-image-side">
      <img src="{{ asset('images/hero/vehicle.jpg') }}" alt="Vehicle">
    </div>
  </div>
</div>
