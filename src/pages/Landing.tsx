import { useEffect, useMemo, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { catalogApi, type CatalogRelease } from "@/lib/catalogApi";

function releaseKey(release: CatalogRelease) {
  return release.slug || release.id;
}

function releaseMeta(release: CatalogRelease) {
  const tracks = release.track_count ?? 0;
  return `${release.type} • ${tracks} track${tracks === 1 ? "" : "s"}`;
}

export function Landing() {
  const navigate = useNavigate();
  const [releases, setReleases] = useState<CatalogRelease[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let mounted = true;

    catalogApi.list()
      .then(({ releases }) => {
        if (!mounted) return;
        setReleases(releases);
        setError(null);
      })
      .catch((err: Error) => {
        if (!mounted) return;
        setError(err.message || "Unable to load releases.");
      })
      .finally(() => {
        if (mounted) setLoading(false);
      });

    return () => {
      mounted = false;
    };
  }, []);

  const featured = releases[0] ?? null;
  const recent = useMemo(() => releases.slice(featured ? 1 : 0, featured ? 5 : 4), [featured, releases]);

  return (
    <div className="min-h-screen overflow-x-hidden bg-surface pb-28 text-on-surface">
      <header className="fixed inset-x-0 top-0 z-50 flex h-14 justify-center border-b border-outline-variant/20 bg-surface/95 backdrop-blur">
        <div className="flex w-full max-w-md items-center justify-between px-gutter">
          <Link to="/" className="text-headline-lg-mobile font-black text-primary">Titan Takuba Music</Link>
          <div className="flex items-center gap-4">
            <Link to="/store" aria-label="Search store" className="text-primary">
              <Icon name="search" />
            </Link>
            <Link to="/login" aria-label="Sign in" className="text-primary">
              <Icon name="person" />
            </Link>
          </div>
        </div>
      </header>

      <main className="mx-auto mt-14 max-w-md px-gutter py-xl">
        {loading && (
          <section className="flex min-h-[60vh] items-center justify-center">
            <Icon name="progress_activity" className="animate-spin text-[42px] text-primary" />
          </section>
        )}

        {!loading && error && (
          <section className="rounded-2xl border border-error/30 bg-error-container/20 p-xl text-center">
            <Icon name="report" className="mx-auto mb-md text-[42px] text-error" />
            <h1 className="text-headline-md font-bold">Catalog unavailable</h1>
            <p className="mt-sm text-body-md text-secondary">{error}</p>
            <button
              onClick={() => window.location.reload()}
              className="mt-lg rounded-full bg-primary-container px-lg py-sm text-label-md font-bold text-on-primary-container"
            >
              Try again
            </button>
          </section>
        )}

        {!loading && !error && !featured && (
          <section className="flex min-h-[60vh] flex-col items-center justify-center rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-xl text-center">
            <div className="mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary/15 text-primary">
              <Icon name="library_music" className="text-[36px]" />
            </div>
            <h1 className="text-headline-lg font-black">No releases yet</h1>
            <p className="mt-sm text-body-md text-secondary">
              Published singles, EPs, and albums will appear here as soon as artists add them.
            </p>
            <div className="mt-lg flex flex-col gap-sm">
              <Link to="/store" className="rounded-full bg-primary-container px-lg py-sm text-label-md font-bold text-on-primary-container">
                Browse store
              </Link>
              <Link to="/login" className="rounded-full border border-outline-variant/30 px-lg py-sm text-label-md text-on-surface">
                Login
              </Link>
            </div>
          </section>
        )}

        {!loading && !error && featured && (
          <>
            <section className="overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-low">
              <div className="relative aspect-square bg-surface-container-high">
                {featured.image ? (
                  <img src={featured.image} alt={featured.title} className="h-full w-full object-cover" />
                ) : (
                  <div className="flex h-full w-full items-center justify-center text-primary">
                    <Icon name="album" className="text-[72px]" />
                  </div>
                )}
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent" />
                <div className="absolute bottom-lg left-lg right-lg">
                  <p className="mb-xs text-label-md uppercase tracking-widest text-primary">Featured release</p>
                  <h1 className="text-display-md font-black text-white">{featured.title}</h1>
                  <p className="mt-xs text-body-md text-white/80">{featured.artist}</p>
                </div>
              </div>
              <div className="p-lg">
                <div className="mb-md flex items-start justify-between gap-md">
                  <p className="text-body-md text-secondary">{releaseMeta(featured)}</p>
                  <p className="shrink-0 text-headline-md font-bold text-primary">${featured.price}</p>
                </div>
                <button
                  onClick={() => navigate(`/store/${releaseKey(featured)}`)}
                  className="flex w-full items-center justify-center gap-sm rounded-xl bg-primary-container py-4 text-headline-md font-bold text-on-primary-container transition-transform active:scale-[0.98]"
                >
                  <Icon name="shopping_bag" />
                  View release
                </button>
              </div>
            </section>

            <section className="mt-xl">
              <div className="mb-lg flex items-end justify-between">
                <h2 className="text-headline-md font-bold">Recent releases</h2>
                <Link to="/store" className="text-label-md font-bold text-primary">View all</Link>
              </div>
              {recent.length > 0 ? (
                <div className="space-y-sm">
                  {recent.map((release) => (
                    <Link
                      key={release.id}
                      to={`/store/${releaseKey(release)}`}
                      className="flex items-center gap-md rounded-xl border border-outline-variant/20 bg-surface-container-low p-sm transition-colors hover:bg-surface-container-high"
                    >
                      <div className="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-surface-container-high text-primary">
                        {release.image ? (
                          <img src={release.image} alt={release.title} className="h-full w-full object-cover" />
                        ) : (
                          <Icon name="album" />
                        )}
                      </div>
                      <div className="min-w-0 flex-1">
                        <h3 className="truncate text-body-lg font-bold">{release.title}</h3>
                        <p className="truncate text-label-md text-secondary">{release.artist}</p>
                        <p className="mt-1 text-label-sm text-secondary">{releaseMeta(release)}</p>
                      </div>
                      <Icon name="chevron_right" className="text-secondary" />
                    </Link>
                  ))}
                </div>
              ) : (
                <div className="rounded-xl border border-outline-variant/20 bg-surface-container-low p-lg text-body-md text-secondary">
                  More releases will appear here when they are published.
                </div>
              )}
            </section>
          </>
        )}
      </main>

      <nav className="fixed bottom-0 left-1/2 z-[60] flex w-full max-w-md -translate-x-1/2 items-center justify-around rounded-t-xl border-t border-outline-variant/20 bg-surface-container-lowest px-2 pb-2 pt-1 shadow-[0_-4px_20px_rgba(0,0,0,0.04)]">
        <Link to="/" className="flex flex-col items-center justify-center text-primary">
          <Icon name="home" filled />
          <span className="text-label-sm">Home</span>
        </Link>
        <Link to="/store" className="flex flex-col items-center justify-center text-on-surface-variant">
          <Icon name="shopping_bag" />
          <span className="text-label-sm">Store</span>
        </Link>
        <Link to="/discover" className="flex flex-col items-center justify-center text-on-surface-variant">
          <Icon name="explore" />
          <span className="text-label-sm">Discover</span>
        </Link>
        <Link to="/scan" className="flex flex-col items-center justify-center text-on-surface-variant">
          <Icon name="qr_code_scanner" />
          <span className="text-label-sm">Redeem</span>
        </Link>
        <Link to="/profile" className="flex flex-col items-center justify-center text-on-surface-variant">
          <Icon name="person" />
          <span className="text-label-sm">Profile</span>
        </Link>
      </nav>
    </div>
  );
}
