import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { FanShell } from "@/components/FanShell";
import { Reveal, StaggerGroup, StaggerItem } from "@/components/Reveal";
import { catalogApi, type CatalogRelease } from "@/lib/catalogApi";

export function Store() {
  const [releases, setReleases] = useState<CatalogRelease[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [failedImages, setFailedImages] = useState<Set<string>>(() => new Set());

  useEffect(() => {
    catalogApi.list()
      .then((res) => setReleases(res.releases))
      .catch((reason) => setError(reason instanceof Error ? reason.message : "Could not load the store."))
      .finally(() => setLoading(false));
  }, []);

  return (
    <FanShell>
      <Reveal direction="down">
        <header className="mb-xl">
          <p className="font-label-md text-label-md uppercase tracking-widest text-primary">Fan library</p>
          <h2 className="mt-xs font-headline-lg text-headline-lg font-bold">Browse releases</h2>
          <p className="mt-xs font-body-md text-body-md text-secondary">
            Every live release on SoundRedeem. Tap an album to preview tracks and unlock the full mix.
          </p>
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

      {!loading && !error && releases.length === 0 && (
        <div className="rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-xl text-center">
          <div className="mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary/15 text-primary">
            <Icon name="album" className="text-[36px]" />
          </div>
          <h3 className="font-headline-md text-headline-md font-bold">No releases yet</h3>
          <p className="mt-sm max-w-md mx-auto text-body-md text-secondary">
            Artists haven't published any live releases yet. Check back soon.
          </p>
        </div>
      )}

      {!loading && releases.length > 0 && (
        <StaggerGroup className="grid grid-cols-2 gap-lg sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5" stagger={0.05}>
          {releases.map((release) => (
            <StaggerItem key={release.id}>
              <Link to={`/store/${release.slug || release.id}`} className="group block">
                <div className="relative mb-md aspect-square overflow-hidden rounded-xl border border-outline-variant/20 bg-surface-container">
                  {release.image && !failedImages.has(release.id) ? (
                    <img
                      src={release.image}
                      alt={release.title}
                      className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                      onError={() => setFailedImages((items) => new Set(items).add(release.id))}
                    />
                  ) : (
                    <div className="flex h-full w-full items-center justify-center">
                      <Icon name="album" className="text-secondary text-[48px]" />
                    </div>
                  )}
                  <div className="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover:opacity-100">
                    <Icon name="play_circle" className="text-white text-[56px]" />
                  </div>
                  <span className="absolute left-2 top-2 rounded-full bg-background/80 px-2 py-0.5 font-label-sm text-label-sm font-bold uppercase tracking-widest text-primary backdrop-blur">
                    {release.type}
                  </span>
                </div>
                <h4 className="truncate font-label-md text-label-md font-bold text-on-surface transition-colors group-hover:text-primary">{release.title}</h4>
                <p className="truncate font-label-sm text-label-sm text-secondary">{release.artist}</p>
                <p className="mt-xs font-label-sm text-label-sm text-primary">${release.price}</p>
              </Link>
            </StaggerItem>
          ))}
        </StaggerGroup>
      )}
    </FanShell>
  );
}
