document.addEventListener('DOMContentLoaded', () => {
  const setSidebar = (open) => document.body.classList.toggle('sidebar-open', open);
  document.querySelectorAll('[data-open-sidebar]').forEach((button) => button.addEventListener('click', () => setSidebar(true)));
  document.querySelectorAll('[data-close-sidebar]').forEach((button) => button.addEventListener('click', () => setSidebar(false)));
  document.querySelectorAll('[data-open-modal]').forEach((button) => button.addEventListener('click', () => {
    const target = document.getElementById(button.dataset.openModal);
    if (target) { target.classList.add('is-open'); target.setAttribute('aria-hidden', 'false'); document.body.classList.add('modal-open'); }
  }));
  document.querySelectorAll('[data-close-modal], [data-modal-backdrop]').forEach((button) => button.addEventListener('click', () => {
    document.querySelectorAll('.modal.is-open').forEach((modal) => { modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); });
    document.body.classList.remove('modal-open');
  }));
  const confirmModal = document.getElementById('confirm-modal');
  let pendingForm = null;
  document.querySelectorAll('[data-confirm]').forEach((element) => element.addEventListener('click', (event) => {
    event.preventDefault(); pendingForm = element.closest('form') || document.querySelector(element.dataset.confirmForm);
    const messageNode = document.getElementById('confirm-message');
    if (messageNode) messageNode.textContent = element.dataset.confirm || 'Apakah kamu yakin ingin melanjutkan?';
    if (confirmModal) { confirmModal.classList.add('is-open'); confirmModal.setAttribute('aria-hidden', 'false'); document.body.classList.add('modal-open'); }
  }));
  document.querySelector('[data-confirm-submit]')?.addEventListener('click', () => { if (pendingForm) pendingForm.submit(); });
  document.querySelectorAll('[data-close-confirm]').forEach((button) => button.addEventListener('click', () => {
    confirmModal?.classList.remove('is-open'); confirmModal?.setAttribute('aria-hidden', 'true'); document.body.classList.remove('modal-open'); pendingForm = null;
  }));
  document.querySelectorAll('.toast-close').forEach((button) => button.addEventListener('click', () => button.closest('.toast')?.remove()));
  window.setTimeout(() => document.querySelectorAll('.toast').forEach((toast) => toast.classList.add('toast-hide')), 5000);
  document.querySelectorAll('[data-amount]').forEach((input) => input.addEventListener('input', () => {
    const raw = input.value.replace(/[^0-9]/g, ''); input.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
  }));
  document.querySelectorAll('form[data-amount-form]').forEach((form) => form.addEventListener('submit', () => {
    form.querySelectorAll('[data-amount]').forEach((input) => { input.value = input.value.replace(/\./g, '').replace(/,/g, '.'); });
  }));
});