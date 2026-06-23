export type CheckoutPurchase = {
  reference: string;
  status: "pending" | "processing" | "succeeded" | "failed" | "cancelled";
  amount: string;
  currency: string;
  method: string;
  provider: string | null;
  provider_ref: string | null;
  email: string | null;
  completed_at: string | null;
  failure_reason: string | null;
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
  const method = (options.method ?? "GET").toUpperCase();
  const headers = new Headers(options.headers);
  headers.set("Accept", "application/json");
  if (!["GET", "HEAD"].includes(method)) {
    headers.set("X-CSRF-TOKEN", await getCsrfToken());
  }
  const response = await fetch(path, { ...options, credentials: "include", headers });
  const data = await response.json().catch(() => ({})) as { message?: string; errors?: Record<string, string[]> };
  if (!response.ok) {
    if (response.status === 419) csrfToken = null;
    const validation = data.errors ? Object.values(data.errors).flat()[0] : undefined;
    throw new Error(validation ?? data.message ?? "The request could not be completed.");
  }
  return data as T;
}

export const checkoutApi = {
  initiate: (body: { asset_key: string | number; email?: string }) =>
    request<{ purchase: CheckoutPurchase; redirect_url: string; sandbox: boolean }>("/api/fan/checkout", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(body),
    }),
  status: (reference: string) =>
    request<{ purchase: CheckoutPurchase }>(`/api/fan/checkout/${encodeURIComponent(reference)}/status`),
};
