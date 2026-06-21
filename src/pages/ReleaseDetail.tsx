import { useEffect, useRef, useState } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import { motion } from "framer-motion";
import { Icon } from "@/components/Icon";
import { FanShell } from "@/components/FanShell";
import { Reveal, StaggerGroup, StaggerItem } from "@/components/Reveal";
import { catalogApi, type CatalogDetail, type CatalogRelease } from "@/lib/catalogApi";

export function ReleaseDetail() {
  const { key = "" } = useParams<{ key: string }>();
  const navigate = useNavigate();
  const [release, setRelease] = useState<CatalogDetail | null>(null);
  const [related, setRelated] = useState<CatalogRelease[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [activeTrackId, setActiveTrackId] = useState<string | null>(null);
  const audioRef = useRef<HTMLAudioElement | null>(null);

  useEffect(() => {
    setLoading(true);
    setError("");
    catalogApi.show(key)
      .then((res) => { setRelease(res.release); setRelated(res.related); })
      .catch((reason) => setError(reason instanceof Error ? reason.message : "Could not load this release."))
      .finally(() => setLoading(false));
  }, [key]);

  useEffect(() => () => {
    audioRef.current?.pause();
    audioRef.current = null;
  }, []);

  function togglePreview(trackId: string, audioUrl: string | null) {
    if (!audioUrl) return;

    if (activeTrackId === trackId && audioRef.current) {
      audioRef.current.pause();
      setActiveTrackId(null);
      return;
    }

    audioRef.current?.pause();
    const audio = new Audio(audioUrl);
    audio.preload = "metadata";
    audio.onended = () => setActiveTrackId(null);
    audio.onerror = () => setActiveTrackId(null);
    audioRef.current = audio;
    setActiveTrackId(trackId);
    void audio.play().catch(() => setActiveTrackId(null));
  }

  function playFirstPreview() {
    const firstPreview = release?.tracks.find((track) => track.audio_url);
    if (firstPreview) togglePreview(firstPreview.id, firstPreview.audio_url);
  }

  if (loading) {
    return (
      <FanShell>
        <div className="flex min-h-[40vh] items-center justify-center">
          <Icon name="progress_activity" className="animate-spin text-primary text-[42px]" />
        </div>
      </FanShell>
    );
  }

  if (error || !release) {
    return (
      <FanShell>
        <div className="rounded-2xl border border-error/30 bg-error-container/20 p-xl text-error">
          <Icon name="error" /> {error || "Release not found."}
          <div className="mt-md">
            <Link to="/store" className="text-primary hover:underline">← Back to store</Link>
          </div>
        </div>
      </FanShell>
    );
  }

  const totalSeconds = release.tracks.reduce((sum, t) => {
    const [m, s] = (t.duration || "0:0").split(":").map(Number);
    return sum + ((m || 0) * 60 + (s || 0));
  }, 0);
  const totalLength = totalSeconds > 0
    ? `${Math.floor(totalSeconds / 60)}m ${String(totalSeconds % 60).padStart(2, "0")}s`
    : "—";

  return (
    <FanShell>
      {/* Breadcrumbs */}
      <Reveal>
        <div className="mb-lg flex items-center gap-sm font-label-md text-label-md text-secondary">
          <Link to="/store" className="hover:text-primary">Store</Link>
          <Icon name="chevron_right" className="text-[16px]" />
          <Link to="/store" className="hover:text-primary">{release.type}</Link>
          <Icon name="chevron_right" className="text-[16px]" />
          <span className="truncate text-on-surface">{release.title}</span>
        </div>
      </Reveal>

      <div className="grid grid-cols-1 items-start gap-xl lg:grid-cols-12">
        {/* Left column: art + buy */}
        <div className="lg:sticky lg:top-24 lg:col-span-5">
          <Reveal direction="up">
            <div className="group relative aspect-square overflow-hidden rounded-2xl border border-outline-variant/20 shadow-2xl">
              {release.image ? (
                <img
                  src={release.image}
                  alt={release.title}
                  className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
              ) : (
                <div className="flex h-full w-full items-center justify-center bg-surface-container">
                  <Icon name="album" className="text-secondary text-[96px]" />
                </div>
              )}
              <div className="absolute inset-0 flex items-end bg-gradient-to-t from-black/60 to-transparent p-lg opacity-0 transition-opacity group-hover:opacity-100">
                <button className="flex items-center gap-sm text-white font-label-md text-label-md">
                  <Icon name="zoom_in" /> View Artwork
                </button>
              </div>
            </div>
          </Reveal>

          <Reveal direction="up" delay={0.1}>
            <div className="mt-lg space-y-md">
              <motion.button
                whileTap={{ scale: 0.98 }}
                onClick={() => navigate(`/checkout/${release.slug || release.id}`)}
                className="flex w-full items-center justify-center gap-md rounded-xl bg-primary-container py-lg font-headline-md text-headline-md font-bold text-on-primary-container shadow-[0_8px_24px_rgba(29,185,84,0.25)] transition-all hover:brightness-110"
              >
                Buy {release.type} - ${release.price}
              </motion.button>
              <div className="grid grid-cols-2 gap-md">
                <button className="flex items-center justify-center gap-sm rounded-xl border border-outline-variant/30 py-md font-label-md text-label-md text-on-surface transition-colors hover:bg-surface-container-high">
                  <Icon name="favorite" /> Wishlist
                </button>
                <button className="flex items-center justify-center gap-sm rounded-xl border border-outline-variant/30 py-md font-label-md text-label-md text-on-surface transition-colors hover:bg-surface-container-high">
                  <Icon name="share" /> Share
                </button>
              </div>
            </div>
          </Reveal>

          <Reveal direction="up" delay={0.15}>
            <div className="mt-xl rounded-2xl border border-outline-variant/20 bg-surface-container-low p-lg">
              <h3 className="mb-md font-label-md text-label-md font-bold uppercase tracking-widest text-primary">
                Release Details
              </h3>
              <div className="space-y-sm font-body-md text-body-md text-secondary">
                <Row label="Release Date" value={release.release_date ?? "—"} />
                <Row label="Type" value={release.type} />
                <Row label="Artist" value={release.artist} />
                <Row label="Total Length" value={totalLength} />
                <Row label="Tracks" value={String(release.tracks.length)} />
              </div>
            </div>
          </Reveal>
        </div>

        {/* Right column: title + tracklist + related */}
        <div className="lg:col-span-7">
          <Reveal direction="left">
            <header className="mb-xl">
              <h1 className="mb-xs font-display-lg text-[44px] font-extrabold leading-[1.05] tracking-[-0.02em] text-on-surface md:text-[56px]">
                {release.title}
              </h1>
              <div className="flex items-center gap-md">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-primary-container text-on-primary-container">
                  <Icon name="person" filled />
                </div>
                <div>
                  <p className="font-headline-md text-headline-md font-bold text-primary">{release.artist}</p>
                  <p className="font-body-md text-body-md text-secondary">Verified Artist · {release.tracks.length} track{release.tracks.length === 1 ? "" : "s"}</p>
                </div>
              </div>
              {release.description && (
                <p className="mt-md max-w-2xl font-body-md text-body-md text-secondary">{release.description}</p>
              )}
            </header>
          </Reveal>

          <section className="mb-xl">
            <Reveal>
              <div className="mb-lg flex items-center justify-between border-b border-outline-variant/20 pb-md">
                <h3 className="font-headline-md text-headline-md font-bold">Tracklist</h3>
                <button
                  type="button"
                  onClick={playFirstPreview}
                  disabled={!release.tracks.some((track) => track.audio_url)}
                  className="flex items-center gap-sm font-label-md text-label-md text-primary hover:underline disabled:cursor-not-allowed disabled:text-secondary disabled:no-underline"
                >
                  <Icon name="play_circle" /> Preview tracks
                </button>
              </div>
            </Reveal>
            <StaggerGroup className="space-y-xs" stagger={0.04}>
              {release.tracks.map((track) => (
                <StaggerItem key={track.id}>
                  <div className="group flex items-center justify-between rounded-xl border border-transparent p-md transition-all duration-200 hover:border-outline-variant/30 hover:bg-surface-container-high">
                    <div className="flex min-w-0 items-center gap-lg">
                      <button
                        type="button"
                        onClick={() => togglePreview(track.id, track.audio_url)}
                        disabled={!track.audio_url}
                        aria-label={track.audio_url ? `${activeTrackId === track.id ? "Pause" : "Play"} ${track.title} preview` : `No preview available for ${track.title}`}
                        className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-secondary transition-colors hover:bg-primary/10 hover:text-primary disabled:cursor-not-allowed disabled:opacity-40"
                      >
                        <span className={activeTrackId === track.id ? "hidden" : "font-label-md text-label-md group-hover:hidden"}>{track.position}</span>
                        <Icon
                          name={activeTrackId === track.id ? "pause" : "play_arrow"}
                          className={activeTrackId === track.id ? "text-primary" : "hidden text-primary group-hover:block"}
                        />
                      </button>
                      <div>
                        <p className="truncate font-body-md text-body-md font-bold text-on-surface transition-colors group-hover:text-primary">
                          {track.title}
                        </p>
                        {!track.audio_url && (
                          <span className="font-label-sm text-label-sm text-secondary">Preview unavailable</span>
                        )}
                        {track.popular && (
                          <span className="mt-xs inline-block rounded-full bg-primary/10 px-2 py-0.5 font-label-sm text-[10px] font-bold uppercase tracking-widest text-primary">
                            Popular
                          </span>
                        )}
                      </div>
                    </div>
                    <div className="flex items-center gap-xl">
                      <span className="font-label-md text-label-md text-secondary">{track.duration}</span>
                      <button className="p-xs text-secondary opacity-0 transition-colors hover:text-primary group-hover:opacity-100">
                        <Icon name="more_vert" />
                      </button>
                    </div>
                  </div>
                </StaggerItem>
              ))}
            </StaggerGroup>
          </section>

          {related.length > 0 && (
            <section className="mb-xl">
              <Reveal>
                <div className="mb-lg flex items-center justify-between">
                  <h3 className="font-headline-md text-headline-md font-bold">Related Releases</h3>
                  <Link to="/store" className="font-label-md text-label-md text-secondary hover:text-primary">View All</Link>
                </div>
              </Reveal>
              <StaggerGroup className="grid grid-cols-2 gap-lg sm:grid-cols-3" stagger={0.06}>
                {related.map((rel) => (
                  <StaggerItem key={rel.id}>
                    <Link to={`/store/${rel.slug || rel.id}`} className="group block">
                      <div className="relative mb-md aspect-square overflow-hidden rounded-xl border border-outline-variant/20">
                        {rel.image ? (
                          <img src={rel.image} alt={rel.title}
                               className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                        ) : (
                          <div className="flex h-full w-full items-center justify-center bg-surface-container">
                            <Icon name="album" className="text-secondary text-[42px]" />
                          </div>
                        )}
                        <div className="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover:opacity-100">
                          <Icon name="play_circle" className="text-white text-[40px]" />
                        </div>
                      </div>
                      <h4 className="truncate font-label-md text-label-md text-on-surface">{rel.title}</h4>
                      <p className="font-label-sm text-label-sm text-secondary">{rel.release_date} · {rel.type}</p>
                    </Link>
                  </StaggerItem>
                ))}
              </StaggerGroup>
            </section>
          )}
        </div>
      </div>
    </FanShell>
  );
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex justify-between">
      <span>{label}</span>
      <span className="text-on-surface">{value}</span>
    </div>
  );
}
