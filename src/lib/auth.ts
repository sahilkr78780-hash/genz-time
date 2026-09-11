export const ADMIN_CREDENTIALS = {
  username: 'admin',
  password: 'sahiltech2026',
  displayName: 'Sahil Verma',
  role: 'Editor-in-Chief & Super Admin',
};

export const AUTH_COOKIE_NAME = 'sahil_tech_admin_session';
export const AUTH_STORAGE_KEY = 'sahil_tech_admin_auth';

export function verifyAdminCredentials(user: string, pass: string): boolean {
  const u = user.trim().toLowerCase();
  const validUser = u === ADMIN_CREDENTIALS.username || u === 'sahil';
  const validPass = pass.trim() === ADMIN_CREDENTIALS.password;
  return validUser && validPass;
}
