export type FanUser = {
  id: number;
  name: string;
  email: string | null;
  phone: string | null;
  avatar_url: string | null;
  has_password: boolean;
};

let csrfToken: string | null = null;

async function getCsrfToken() {
  if (csrfToken) return csrfToken;

  const response = await fetch("/api/fan/csrf", { credentials: "include" });
  const data = await response.json() as { token: string };
  csrfToken = data.token;
  return csrfToken;
}

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const method = options.method?.toUpperCase() ?? "GET";
  const headers = new Headers(options.headers);
  headers.set("Accept", "application/json");

  if (!["GET", "HEAD"].includes(method)) {
    headers.set("X-CSRF-TOKEN", await getCsrfToken());
  }

  const response = await fetch(path, {
    ...options,
    credentials: "include",
    headers,
  });

  const data = await response.json().catch(() => ({})) as {
    message?: string;
    errors?: Record<string, string[]>;
  };

  if (!response.ok) {
    if (response.status === 419) csrfToken = null;
    const validationMessage = data.errors
      ? Object.values(data.errors).flat()[0]
      : undefined;
    throw new Error(validationMessage ?? data.message ?? "The request could not be completed.");
  }

  return data as T;
}

function register(body: Record<string, string>) {
  return request<{ user: FanUser }>("/api/fan/register", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body),
  });
}

export const fanApi = {
  registerEmail: (name: string, email: string, password: string) => register({
    method: "email",
    name,
    email,
    password,
  }),
  registerPhone: (phone: string) => register({
    method: "phone",
    phone,
  }),
  login: (email: string, password: string, remember: boolean) =>
    request<{ user: FanUser }>("/api/fan/login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password, remember }),
    }),
  logout: () => request<{ message: string }>("/api/fan/logout", { method: "POST" }),
  me: () => request<{ user: FanUser }>("/api/fan/me"),
  updateProfile: (form: FormData) => request<{ message: string; user: FanUser }>("/api/fan/profile", {
    method: "POST",
    body: form,
  }),
  updatePassword: (currentPassword: string, password: string, passwordConfirmation: string) =>
    request<{ message: string }>("/api/fan/password", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        current_password: currentPassword,
        password,
        password_confirmation: passwordConfirmation,
      }),
    }),
};
