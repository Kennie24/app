import { useEffect, useRef, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { Icon } from "@/components/Icon";

const PREVIEW_LIMIT = 120;

const tracks = [
  {
    id: "sunlight",
    title: "Sunlight",
    duration: "3:42",
    thumb: "https://lh3.googleusercontent.com/aida-public/AB6AXuBaQUHOV2H7MxTGAgfazDgH1KcsCJyhHi8BiZz7ty9nM4H6_5Up4_gmNdPSnia0TdQEDJK3s6MPDrC4VQuZIxGxHl3E8ioyuahBncDO0dAU0OXXtcAgOLRsatFasb1hg9r8ab6qLAhYzp1zRLnYRjMhmOudZzVwIswPiBztxOG7IPMcx6IYiU0aOS4u0uJcTQ-AuGw6pV8mpbJ7WZ21SRKMO6kbxEjEbG_MrGZAI3Rp2lxdTcZqn4xpZjZ_FF-eF3Wh0ctQ-CR6UHce",
  },
  {
    id: "breeze",
    title: "Breeze",
    duration: "4:15",
    thumb: "https://lh3.googleusercontent.com/aida-public/AB6AXuAL9JoyqhGFT3USeTBpOOkBDioZrnqB0mFX3brhlDbbzXc74JBISNWYQ-MiicOlJWDYKuyvTWQZdO4y4IgSujfrYUGW9lN5r7QHuiN3Vt88WLK-uqc78vJ3j3X16J6rHDbBaU4rk-Pb7NdTH3kTzSNu9r_KyRlTi9iezjttaEAurmftKBDl-BcZqzfgI5d1QpquTvoNbLyAugwLjpmslLBYwUakFkBUFUpT82kXzE0JY05usMcF9J_yAs5KbZN0pbmMG9SdLgfloXzS",
  },
];

export function Landing() {
  const navigate = useNavigate();
  const [playing, setPlaying] = useState(true);
  const [elapsed, setElapsed] = useState(78);
  const intervalRef = useRef<ReturnType<typeof setInterval> | null>(null);

  useEffect(() => {
    if (playing && elapsed < PREVIEW_LIMIT) {
      intervalRef.current = setInterval(() => {
        setElapsed((prev) => {
          if (prev >= PREVIEW_LIMIT) { setPlaying(false); return PREVIEW_LIMIT; }
          return prev + 1;
        });
      }, 1000);
    }
    return () => { if (intervalRef.current) clearInterval(intervalRef.current); };
  }, [playing, elapsed]);

  const progress = Math.min((elapsed / PREVIEW_LIMIT) * 100, 100);
  const fmt = (s: number) => `${Math.floor(s / 60)}:${String(s % 60).padStart(2, "0")}`;

  return (
    <div className="min-h-screen overflow-x-hidden bg-surface pb-32 text-on-surface">
      {/* Top Bar */}
      <header className="fixed inset-x-0 top-0 z-50 flex h-14 items-center justify-between bg-surface px-gutter shadow-sm">
        <span className="text-headline-lg-mobile font-black text-primary">Orange Music</span>
        <div className="flex gap-4">
          <Icon name="search" className="cursor-pointer text-primary" />
          <Icon name="notifications" className="cursor-pointer text-primary" />
        </div>
      </header>

      <main className="mt-14">
        {/* Artist Hero */}
        <section className="relative flex h-[530px] w-full flex-col justify-end">
          <div className="absolute inset-0 z-0">
            <div
              className="h-full w-full bg-cover bg-center"
              style={{ backgroundImage: "url('https://lh3.googleusercontent.com/aida-public/AB6AXuAzroXUEpr1GLy726CjEcsYZYYOvm3kHKDJdJmPNzIx-0menUm5pjzsKH35w-95GB4V-2eRQtBcrNVg9Fwsjiipeq1OMtviM7FyKkOIjTRQTC781U79QZBT0jM0rsGy8BUBobTlI5J9IIdSAW9449d_pL1ohhLF6K3Kxmim90_12byyQ-uv5VXWTNEcnZgxqtLVlA8v7GSRsA4wk5YQqnjTB9VrbZrg0lRyBLQdZCjoG9e3lFso4o1NJwW3Ilvz3jNAlfsQLGs_20hM')" }}
            />
            <div className="absolute inset-0 bg-gradient-to-t from-surface via-surface/40 to-transparent" />
          </div>

          <div className="relative z-10 px-gutter pb-xl">
            <div className="mb-2 flex items-center gap-1">
              <Icon name="verified" className="text-[18px] text-primary" filled />
              <span className="text-label-md uppercase tracking-wider text-primary">Verified Artist</span>
            </div>
            <h1 className="text-display-lg mb-2">The Wanderer</h1>
            <div className="mb-md flex gap-xl">
              <div>
                <p className="text-headline-md text-primary">2.4M</p>
                <p className="text-label-sm text-on-surface-variant">Monthly Listeners</p>
              </div>
              <div>
                <p className="text-headline-md text-primary">840K</p>
                <p className="text-label-sm text-on-surface-variant">Followers</p>
              </div>
            </div>
            <p className="mb-lg max-w-md text-body-md leading-relaxed text-on-surface-variant">
              Sculpting soundscapes from the quiet moments of the road. Electronic textures meet organic folk roots in a journey of sonic exploration.
            </p>
            <div className="flex gap-md">
              <button className="flex items-center gap-2 rounded-full bg-primary-container px-xl py-3 text-label-md text-on-primary-container transition-all active:scale-95">
                <Icon name="person_add" className="text-[20px]" filled />
                Follow
              </button>
              <button className="rounded-full border border-outline-variant px-xl py-3 text-label-md text-on-surface transition-all active:bg-surface-container-high">
                Share
              </button>
            </div>
          </div>
        </section>

        {/* Music & Merch */}
        <section className="mt-xl px-gutter">
          <div className="mb-lg flex items-end justify-between">
            <h2 className="text-headline-md">Music &amp; Merch</h2>
            <Link to="/store" className="text-label-md text-primary">View All</Link>
          </div>

          {/* Featured Album */}
          <div className="mb-xl overflow-hidden rounded-xl border border-outline-variant bg-surface-container-low">
            <div className="relative aspect-square">
              <img
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuC1I7RDJqA2nb9-mBbExELYSoQGcQQt6Q4paYRD3d38uJ1cidWpxrc0ZdN9Rj33Bj9RRPNrPLi4epmb_TJLi7DwU51mFw3S6DZ7jVx6Km075KhIhPHGn_iBQEiLLtUq2POmYPIS6lguZF2t6yhKRSvPbVn025J07qyMG5a95MoSjDua4yr9ztkFfafgGewnMIqJkm7ln9A1nbqxd-B2XJXXfNY42DAa9xqPYR-T8kdDEjj_16c070Zu-Zfug0_7tKjXFUHIWn6dQtVz"
                alt="Echoes of Summer album cover"
                className="h-full w-full object-cover"
              />
              <div className="absolute right-4 top-4 rounded-full bg-primary-container px-3 py-1 text-label-sm text-on-primary-container">
                New Release
              </div>
            </div>
            <div className="p-lg">
              <div className="mb-md flex items-start justify-between">
                <div>
                  <h3 className="text-headline-md">Echoes of Summer</h3>
                  <p className="text-body-md text-on-surface-variant">Album · 12 Tracks</p>
                </div>
                <p className="text-headline-md text-primary">$12.99</p>
              </div>
              <button
                onClick={() => navigate("/store")}
                className="w-full rounded-xl bg-primary-container py-4 text-headline-md text-on-primary-container transition-transform active:scale-[0.98]"
              >
                Buy Album
              </button>
            </div>
          </div>

          {/* Track Previews */}
          <div className="space-y-4">
            <h3 className="mb-4 text-label-md uppercase tracking-widest text-on-surface-variant">Track Previews</h3>
            {tracks.map((track) => (
              <div
                key={track.id}
                onClick={() => setPlaying((p) => !p)}
                className="group flex cursor-pointer items-center justify-between rounded-xl border border-outline-variant bg-surface-container-low p-4 transition-colors hover:bg-surface-container-high"
              >
                <div className="flex items-center gap-4">
                  <div className="relative h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg">
                    <img src={track.thumb} alt={track.title} className="h-full w-full object-cover" />
                    <div className="absolute inset-0 flex items-center justify-center bg-black/40">
                      <Icon name="play_arrow" className="text-white" filled />
                    </div>
                  </div>
                  <div>
                    <p className="text-label-md">{track.title}</p>
                    <p className="text-label-sm text-on-surface-variant">{track.duration} · 2m Preview</p>
                  </div>
                </div>
                <Icon name="more_vert" className="text-on-surface-variant transition-colors group-hover:text-primary" />
              </div>
            ))}
          </div>
        </section>

        {/* Inner Circle CTA */}
        <section className="my-xl px-gutter">
          <div className="relative overflow-hidden rounded-2xl bg-primary-container p-xl text-on-primary-container">
            <div className="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl" />
            <div className="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-black/10 blur-3xl" />
            <h2 className="relative z-10 text-headline-lg mb-2">Join the Inner Circle</h2>
            <p className="relative z-10 mb-lg text-body-md opacity-90">
              Get exclusive access to pre-releases, limited vinyl, and secret tour dates.
            </p>
            <div className="relative z-10 flex flex-col gap-sm">
              <input
                type="email"
                placeholder="Enter your email"
                className="rounded-xl border-none bg-black/20 px-lg py-4 text-on-primary-container placeholder:text-on-primary-container/60 focus:outline-none focus:ring-2 focus:ring-white/40"
              />
              <button className="rounded-xl bg-surface py-4 text-headline-md text-primary transition-transform active:scale-[0.98]">
                Subscribe Now
              </button>
            </div>
          </div>
        </section>
      </main>

      {/* Bottom Nav */}
      <nav className="fixed bottom-0 z-[60] flex w-full items-center justify-around rounded-t-xl bg-surface-container-lowest px-2 pb-2 pt-1 shadow-[0_-4px_20px_rgba(0,0,0,0.04)]">
        <Link to="/" className="flex flex-col items-center justify-center text-primary">
          <Icon name="home" filled />
          <span className="text-label-sm">Home</span>
        </Link>
        <Link to="/store" className="flex flex-col items-center justify-center text-on-surface-variant">
          <Icon name="shopping_bag" />
          <span className="text-label-sm">Store</span>
        </Link>
        <Link to="/scan" className="flex flex-col items-center justify-center rounded-full bg-secondary-container px-4 py-1 text-on-surface">
          <Icon name="library_music" />
          <span className="text-label-sm">Library</span>
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

      {/* Mini Player */}
      <div
        className="fixed bottom-[68px] left-2 right-2 z-50 rounded-2xl p-3 shadow-2xl"
        style={{ backdropFilter: "blur(12px)", background: "rgba(30,30,30,0.85)", borderTop: "1px solid #2C2C2C" }}
      >
        <div className="absolute -top-10 left-1/2 -translate-x-1/2 flex items-center gap-2 whitespace-nowrap rounded-full bg-surface-container-high px-4 py-2 shadow-lg animate-bounce" style={{ animationDuration: "2000ms" }}>
          <Icon name="info" className="text-[16px] text-primary" />
          <span className="text-label-sm text-on-surface">Enjoying the preview? Log in for full tracks.</span>
        </div>

        <div className="flex items-center justify-between gap-4">
          <div className="flex min-w-0 items-center gap-3">
            <div className="relative h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-surface-container-highest">
              <img src={tracks[0].thumb} alt="Now playing" className="h-full w-full object-cover" />
              <div className="absolute inset-0 flex items-center justify-center bg-primary/20">
                <div className="mx-[1px] h-3 w-1 animate-pulse bg-primary" />
                <div className="mx-[1px] h-5 w-1 animate-pulse bg-primary" style={{ animationDelay: "0.2s" }} />
                <div className="mx-[1px] h-2 w-1 animate-pulse bg-primary" style={{ animationDelay: "0.4s" }} />
              </div>
            </div>
            <div className="min-w-0 overflow-hidden">
              <p className="truncate text-label-md">Sunlight (Preview)</p>
              <p className="truncate text-label-sm text-on-surface-variant">The Wanderer</p>
            </div>
          </div>
          <div className="flex items-center gap-4">
            <button onClick={() => setPlaying((p) => !p)} className="text-primary">
              <Icon name={playing ? "pause_circle" : "play_circle"} className="text-[32px]" filled />
            </button>
            <Icon name="skip_next" className="cursor-pointer text-on-surface-variant" />
          </div>
        </div>

        <div className="mt-3 h-1 w-full overflow-hidden rounded-full bg-surface-container-highest">
          <div className="h-full bg-primary-container transition-all duration-1000" style={{ width: `${progress}%` }} />
        </div>
        <div className="mt-1 flex justify-between px-1">
          <span className="text-[10px] text-on-surface-variant">{fmt(elapsed)}</span>
          <span className="text-[10px] text-primary">{fmt(PREVIEW_LIMIT)} Limit</span>
        </div>
      </div>
    </div>
  );
}
