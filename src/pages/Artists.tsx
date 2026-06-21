import { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { FanShell } from "@/components/FanShell";
import { Reveal, StaggerGroup, StaggerItem } from "@/components/Reveal";
import { catalogApi, type CatalogRelease } from "@/lib/catalogApi";

type ArtistSummary = {
  name: string;
  releaseCount: number;
  trackCount: number;
  latestImage: string | null;
  latestRelease: CatalogRelease;
};

export function Artists() {
  const [releases, setReleases] = useState<CatalogRelease[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [query, setQuery] = useState("");

  useEffect(() => {
    catalogApi.list()
      .then((res) => setReleases(res.releases))
      .catch((reason) => setError(reason instanceof Error ? reason.message : "Could not load artists."))
      .finally(() => setLoading(false));
  }, []);

  const artists: ArtistSummary[] = useMemo(() => {
    const map = new Map<string, ArtistSummary>();
    for (const r of releases) {
      const name = r.artist?.trim();
      if (!name) continue;
      const existing = map.get(name);
      if (existing) {
        existing.releaseCount += 1;
        existing.trackCount += r.track_count ?? 0;
        if (!existing.latestImage && r.image) existing.latestImage = r.image;
      } else {
        map.set(name, {
          name,
          releaseCount: 1,
          trackCount: r.track_count ?? 0,
          latestImage: r.image,
          latestRelease: r,
        });
      }
    }
    return Array.from(map.values()).sort((a, b) => b.releaseCount - a.releaseCount);
  }, [releases]);

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();
    if (!q) return artists;
    return artists.filter((a) => a.name.toLowerCase().includes(q));
  }, [artists, query]);

  return (
    <FanShell>
      <Reveal direction="down">
        <header className="mb-xl flex flex-col gap-md md:flex-row md:items-end md:justify-between">
          <div>
            <p className="font-label-md text-label-md uppercase tracking-widest text-primary">Artists</p>
            <h2 className="mt-xs font-headline-lg text-headline-lg font-bold">Verified creators</h2>
            <p className="mt-xs font-body-md text-body-md text-secondary">
              Every artist with a live release on SoundRedeem.
            </p>
          </div>
          <div className="relative w-full md:w-72">
            <Icon name="search" className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-secondary text-[20px]" />
            <input
              type="search"
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Search artists…"
              className="w-full rounded-full border border-outline-variant/30 bg-background py-xs pl-10 pr-md font-body-md text-body-md outline-none transition-colors focus:border-primary"
            />
          </div>
        </header>
      </Reveal>

      {loading && (
        <div className="flex min-h-[40vh] items-center justify-center">
          <Icon name="progress_activity" className="animate-spin text-primary text-[42px]" />
        </div>
      )}

      {error && !loading && (
        <div role="alert" className="flex items-center gap-sm rounded-xl border border-error/30 bg-error-container/20 p-md text-error">
          <Icon name="error" /><span>{error}</span>
        </div>
      )}

      {!loading && !error && artists.length === 0 && (
        <div className="rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-xl text-center">
          <div className="mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary/15 text-primary">
            <Icon name="person" className="text-[36px]" />
          </div>
          <h3 className="font-headline-md text-headline-md font-bold">No artists yet</h3>
          <p className="mt-sm max-w-md mx-auto text-body-md text-secondary">
            When verified artists publish releases, they'll appear here.
          </p>
        </div>
      )}

      {!loading && filtered.length === 0 && artists.length > 0 && (
        <div className="rounded-xl border border-outline-variant/30 bg-surface-container-low p-lg text-center text-secondary">
          No artists match "{query}".
        </div>
      )}

      {!loading && filtered.length > 0 && (
        <StaggerGroup className="grid grid-cols-1 gap-lg sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" stagger={0.05}>
          {filtered.map((artist) => (
            <StaggerItem key={artist.name}>
              <Link
                to={`/store/${artist.latestRelease.slug || artist.latestRelease.id}`}
                className="group flex flex-col gap-md rounded-2xl border border-outline-variant/20 bg-surface-container-low p-lg transition-all hover:border-primary/40 hover:bg-surface-container"
              >
                <div className="flex items-center gap-md">
                  <div className="relative h-16 w-16 shrink-0 overflow-hidden rounded-full border border-outline-variant/20 bg-surface-container">
                    {artist.latestImage ? (
                      <img src={artist.latestImage} alt={artist.name} className="h-full w-full object-cover" />
                    ) : (
                      <div className="flex h-full w-full items-center justify-center text-primary">
                        <Icon name="person" filled />
                      </div>
                    )}
                  </div>
                  <div className="min-w-0">
                    <h3 className="truncate font-headline-md text-headline-md font-bold text-on-surface group-hover:text-primary">
                      {artist.name}
                    </h3>
                    <p className="font-label-md text-label-md text-secondary">Verified artist</p>
                  </div>
                </div>
                <div className="flex items-center justify-between border-t border-outline-variant/20 pt-md font-label-md text-label-md text-secondary">
                  <span><strong className="text-on-surface">{artist.releaseCount}</strong> release{artist.releaseCount === 1 ? "" : "s"}</span>
                  {artist.trackCount > 0 && (
                    <span><strong className="text-on-surface">{artist.trackCount}</strong> track{artist.trackCount === 1 ? "" : "s"}</span>
                  )}
                </div>
                <span className="inline-flex items-center gap-sm font-label-md text-label-md text-primary group-hover:underline">
                  Open latest release <Icon name="arrow_forward" />
                </span>
              </Link>
            </StaggerItem>
          ))}
        </StaggerGroup>
      )}
    </FanShell>
  );
}
