export type ArtistUser = {
  id: number;
  name: string;
  email: string;
  artist_name: string;
};

export type ArtistTrack = {
  id: string;
  title: string;
  fileName: string;
  audioUrl?: string;
  previewUrl?: string;
  samplePlays?: number;
};

export type ArtistRelease = {
  id: string;
  title: string;
  type: "Single" | "Album";
  status: "Live" | "Draft";
  price: string;
  image: string;
  tracks: ArtistTrack[];
  redemptions: number;
  limit: number;
};

let csrfToken: string | null = null;

async function getCsrfToken() {
  if (csrfToken) return csrfToken;
  const response = await fetch("/api/artist/csrf", { credentials: "include" });
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
    headers,
    credentials: "include",
  });

  const data = await response.json().catch(() => ({})) as Record<string, unknown>;
  if (!response.ok) {
    if (response.status === 419) csrfToken = null;
    throw new Error(typeof data.message === "string" ? data.message : "The request could not be completed.");
  }

  return data as T;
}

export const artistApi = {
  checkEmail: (email: string) => request<{ name: string }>("/api/artist/check-email", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email }),
  }),
  login: async (email: string, password: string, remember: boolean) => {
    const result = await request<{ user: ArtistUser }>("/api/artist/login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password, remember }),
    });
    csrfToken = null;
    return result;
  },
  me: () => request<{ user: ArtistUser }>("/api/artist/me"),
  logout: async () => {
    const result = await request<{ message: string }>("/api/artist/logout", { method: "POST" });
    csrfToken = null;
    return result;
  },
  releases: () => request<{ releases: ArtistRelease[] }>("/api/artist/releases"),
  createRelease: (form: FormData) => request<{ message: string; release: ArtistRelease }>("/api/artist/releases", { method: "POST", body: form }),
  samplePlayed: (releaseId: string, trackId: string) => request<{ sample_plays: number }>(`/api/artist/releases/${releaseId}/tracks/${trackId}/sample-played`, { method: "POST" }),
};
