export type CatalogRelease = {
  id: string;
  slug: string;
  title: string;
  artist: string;
  type: "Single" | "EP" | "Album";
  price: string;
  image: string | null;
  release_date: string | null;
  track_count: number | null;
};

export type CatalogTrack = {
  id: string;
  position: number;
  title: string;
  duration: string;
  audio_url: string | null;
  sample_plays: number;
  popular: boolean;
};

export type CatalogDetail = CatalogRelease & {
  description: string | null;
  tracks: CatalogTrack[];
};

async function request<T>(path: string): Promise<T> {
  const response = await fetch(path, {
    headers: { Accept: "application/json" },
    credentials: "include",
  });
  const data = await response.json().catch(() => ({})) as Record<string, unknown>;
  if (!response.ok) {
    throw new Error(typeof data.message === "string" ? data.message : "Unable to load catalog.");
  }
  return data as T;
}

export const catalogApi = {
  list: () => request<{ releases: CatalogRelease[] }>("/api/catalog"),
  show: (key: string) => request<{ release: CatalogDetail; related: CatalogRelease[] }>(`/api/catalog/${encodeURIComponent(key)}`),
};
