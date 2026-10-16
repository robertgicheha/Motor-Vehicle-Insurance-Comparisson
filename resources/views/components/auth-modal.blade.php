<div class="auth-modal-overlay" id="authModal" style="display:none" x-data="{
  username: '', password: '',
  loading: false, error: '', errors: {},
  redirectTo: sessionStorage.getItem('auth_redirect') || '{{ route('dashboard') }}',
  validateLogin() {
    this.errors = {};
    if (!this.username.trim()) {
      this.errors.username = 'Username is required';
    }
    if (!this.password) {
      this.errors.password = 'Password is required';
    }
    return Object.keys(this.errors).length === 0;
  },
  submitLogin($event) {
    if (!this.validateLogin()) { $event.preventDefault(); return; }
    $event.preventDefault();
    this.loading = true;
    this.error = '';
    this.errors = {};
    var self = this;
    var form = $event.target;
    var formData = new FormData(form);
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    }).then(function(response) {
      return response.json().then(function(data) {
        if (response.ok) {
          window.location.href = data.redirect || self.redirectTo;
        } else {
          self.loading = false;
          if (data.errors) {
            var msgs = [];
            if (data.errors.username) msgs.push(data.errors.username);
            if (data.errors.password) msgs.push(data.errors.password);
            if (msgs.length > 1) {
              showToastError('Your credentials are invalid, check if the username or password are correct and try again');
            } else if (msgs.length === 1) {
              showToastError(msgs[0]);
            }
          } else if (data.message) {
            showToastError(data.message);
          } else {
            showToastError('Your credentials are invalid, check if the username or password are correct and try again');
          }
        }
      });
    }).catch(function() {
      self.loading = false;
      showToastError('Your credentials are invalid, check if the username or password are correct and try again');
    });
  }
}">
  <div class="auth-modal-split" @click.stop>
    <button class="auth-modal-close" onclick="closeAuthModal()">&times;</button>

    <!-- Left: Form -->
    <div class="auth-modal-form-side">
      <div class="auth-modal-form-wrap">
        <div class="auth-tabs">
          <button class="auth-tab active" type="button">Login</button>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" @submit="submitLogin($event)">
          @csrf
          <input type="hidden" name="redirect_to" :value="redirectTo">
          <div class="auth-field">
            <label for="auth-username">Username</label>
            <input type="text" id="auth-username" name="username" x-model="username" required placeholder="user1_westlands" autocomplete="username">
            <p class="auth-error" x-show="errors.username" x-text="errors.username"></p>
          </div>
          <div class="auth-field">
            <label for="auth-password">Password</label>
            <input type="password" id="auth-password" name="password" x-model="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="current-password">
            <p class="auth-error" x-show="errors.password" x-text="errors.password"></p>
          </div>
          <button type="submit" class="btn btn-primary auth-submit" :disabled="loading">
            <span x-show="!loading">Login</span>
            <span x-show="loading" class="auth-spinner">
              <svg class="auth-spinner-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
              Please wait...logging in
            </span>
          </button>

          <p class="auth-switch">Accounts are issued by your administrator. Contact them if you need access.</p>
        </form>
      </div>
    </div>

    <!-- Right: Image -->
    <div class="auth-modal-image-side">
      <img src="{{ asset('images/hero/vehicle.jpg') }}" alt="Vehicle" width="1920" height="1280">
    </div>
  </div>
</div>

<style>
.auth-spinner {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.auth-spinner-icon {
  width: 16px;
  height: 16px;
  animation: auth-spin 1s linear infinite;
}
@keyframes auth-spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
