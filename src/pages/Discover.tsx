import { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { FanShell } from "@/components/FanShell";
import { Reveal, StaggerGroup, StaggerItem } from "@/components/Reveal";
import { catalogApi, type CatalogRelease } from "@/lib/catalogApi";

export function Discover() {
  const [releases, setReleases] = useState<CatalogRelease[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    catalogApi.list()
      .then((res) => setReleases(res.releases))
      .catch((reason) => setError(reason instanceof Error ? reason.message : "Could not load discover."))
      .finally(() => setLoading(false));
  }, []);

  const featured = releases[0];
  const fresh = useMemo(() => releases.slice(1, 7), [releases]);
  const albums = useMemo(() => releases.filter((r) => r.type === "Album").slice(0, 6), [releases]);
  const eps = useMemo(() => releases.filter((r) => r.type === "EP").slice(0, 6), [releases]);
  const singles = useMemo(() => releases.filter((r) => r.type === "Single").slice(0, 6), [releases]);

  return (
    <FanShell>
      <Reveal direction="down">
        <header className="mb-xl">
          <p className="font-label-md text-label-md uppercase tracking-widest text-primary">Discover</p>
          <h2 className="mt-xs font-headline-lg text-headline-lg font-bold">What's new on SoundRedeem</h2>
          <p className="mt-xs font-body-md text-body-md text-secondary">
            A live feed of the freshest verified releases from our artists.
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
        <EmptyState />
      )}

      {!loading && featured && (
        <Reveal direction="up">
          <Link
            to={`/store/${featured.slug || featured.id}`}
            className="group relative mb-xl flex flex-col overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-low md:flex-row"
          >
            <div className="relative aspect-square w-full overflow-hidden md:aspect-auto md:w-1/2">
              {featured.image ? (
                <img src={featured.image} alt={featured.title}
                     className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />
              ) : (
                <div className="flex h-full w-full items-center justify-center bg-surface-container">
                  <Icon name="album" className="text-secondary text-[96px]" />
                </div>
              )}
              <span className="absolute left-4 top-4 rounded-full bg-primary px-3 py-1 font-label-sm text-label-sm font-bold uppercase tracking-widest text-on-primary">
                Featured
              </span>
            </div>
            <div className="flex flex-col justify-center gap-md p-xl md:w-1/2">
              <p className="font-label-md text-label-md uppercase tracking-widest text-primary">{featured.type}</p>
              <h3 className="font-display-lg text-[36px] font-extrabold leading-tight tracking-tight text-on-surface md:text-[44px]">
                {featured.title}
              </h3>
              <p className="font-headline-md text-headline-md text-secondary">{featured.artist}</p>
              <div className="flex items-center gap-md">
                <span className="font-headline-md text-headline-md font-bold text-primary">${featured.price}</span>
                {featured.release_date && (
                  <span className="font-label-md text-label-md text-secondary">· {featured.release_date}</span>
                )}
              </div>
              <span className="mt-sm inline-flex items-center gap-sm font-label-md text-label-md text-primary group-hover:underline">
                Open release <Icon name="arrow_forward" />
              </span>
            </div>
          </Link>
        </Reveal>
      )}

      {!loading && fresh.length > 0 && (
        <Section title="Fresh Drops" subtitle="Most recent uploads from verified artists">
          <Grid releases={fresh} />
        </Section>
      )}

      {!loading && albums.length > 0 && (
        <Section title="Albums" subtitle="Long-players you can redeem end-to-end">
          <Grid releases={albums} />
        </Section>
      )}

      {!loading && eps.length > 0 && (
        <Section title="EPs" subtitle="Focused multi-track releases from verified artists">
          <Grid releases={eps} />
        </Section>
      )}

      {!loading && singles.length > 0 && (
        <Section title="Singles" subtitle="One-track moments worth owning">
          <Grid releases={singles} />
        </Section>
      )}
    </FanShell>
  );
}

function Section({ title, subtitle, children }: { title: string; subtitle: string; children: React.ReactNode }) {
  return (
    <section className="mb-xl">
      <Reveal>
        <div className="mb-lg flex items-end justify-between border-b border-outline-variant/20 pb-md">
          <div>
            <h3 className="font-headline-md text-headline-md font-bold">{title}</h3>
            <p className="font-label-md text-label-md text-secondary">{subtitle}</p>
          </div>
          <Link to="/store" className="font-label-md text-label-md text-secondary hover:text-primary">View all</Link>
        </div>
      </Reveal>
      {children}
    </section>
  );
}

function Grid({ releases }: { releases: CatalogRelease[] }) {
  return (
    <StaggerGroup className="grid grid-cols-2 gap-lg sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6" stagger={0.04}>
      {releases.map((release) => (
        <StaggerItem key={release.id}>
          <Link to={`/store/${release.slug || release.id}`} className="group block">
            <div className="relative mb-md aspect-square overflow-hidden rounded-xl border border-outline-variant/20 bg-surface-container">
              {release.image ? (
                <img src={release.image} alt={release.title}
                     className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" />
              ) : (
                <div className="flex h-full w-full items-center justify-center">
                  <Icon name="album" className="text-secondary text-[48px]" />
                </div>
              )}
              <div className="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover:opacity-100">
                <Icon name="play_circle" className="text-white text-[56px]" />
              </div>
            </div>
            <h4 className="truncate font-label-md text-label-md font-bold text-on-surface group-hover:text-primary">{release.title}</h4>
            <p className="truncate font-label-sm text-label-sm text-secondary">{release.artist}</p>
          </Link>
        </StaggerItem>
      ))}
    </StaggerGroup>
  );
}

function EmptyState() {
  return (
    <div className="rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-xl text-center">
      <div className="mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary/15 text-primary">
        <Icon name="explore" className="text-[36px]" />
      </div>
      <h3 className="font-headline-md text-headline-md font-bold">Nothing to discover yet</h3>
      <p className="mt-sm max-w-md mx-auto text-body-md text-secondary">
        When verified artists publish releases, they'll show up here first.
      </p>
    </div>
  );
}
