document.getElementById('loginForm').addEventListener('submit', function(e) {
  const message = document.getElementById('message'); 
  message.classList.remove('hidden');
  message.classList.add('show');

  setTimeout(() => {
    message.classList.remove('show');
    message.classList.add('hidden');
  }, 3000);
});

const passwordInput = document.getElementById('password');
const toggleBtn = document.getElementById('togglePassword');
const eyeIcon = document.getElementById('eyeIcon');

toggleBtn.addEventListener('mousedown', () => {
  passwordInput.type = 'text';
  eyeIcon.textContent = 'visibility'; // icono de ojo abierto
});

toggleBtn.addEventListener('mouseup', () => {
  passwordInput.type = 'password';
  eyeIcon.textContent = 'visibility_off'; // icono de ojo cerrado
});

toggleBtn.addEventListener('mouseleave', () => {
  passwordInput.type = 'password';
  eyeIcon.textContent = 'visibility_off';
});
