import { type ReactNode, useEffect, useState } from "react";
import { NavLink, Outlet, useLocation } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { BottomNav } from "@/components/BottomNav";
import { cn } from "@/lib/utils";
import { fanApi, type FanUser } from "@/lib/fanApi";

const sideNav = [
  { to: "/scan", icon: "qr_code_scanner", label: "Redeem" },
  { to: "/store", icon: "library_music", label: "Music" },
  { to: "/profile", icon: "person", label: "Profile" },
];

const topNav = [
  { to: "/discover", label: "Discover", end: false },
  { to: "/store", label: "Store", end: false },
  { to: "/artists", label: "Artists", end: false },
  { to: "/community", label: "Community", end: false },
];

export function FanShell({ children }: { children?: ReactNode }) {
  const location = useLocation();
  const [fan, setFan] = useState<FanUser | null>(null);

  useEffect(() => {
    fanApi.me()
      .then(({ user }) => setFan(user))
      .catch(() => setFan(null));
  }, []);

  const initials = fan?.name
    .split(/\s+/)
    .map((part) => part[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();
  return (
    <div className="min-h-screen bg-background text-on-background">
      {/* Sidebar (desktop) */}
      <aside className="fixed left-0 top-0 z-40 hidden h-full w-64 flex-col gap-sm border-r border-outline-variant/20 bg-surface-container-low p-md md:flex">
        <div className="mb-xl px-sm">
          <h1 className="font-headline-md text-headline-md font-bold text-primary">SoundRedeem</h1>
          <p className="font-label-sm text-label-sm text-secondary uppercase tracking-widest">Fan Studio</p>
        </div>

        <nav className="flex flex-1 flex-col gap-xs">
          {sideNav.map((item) => {
            const active = location.pathname === item.to ||
              (item.to !== "/" && location.pathname.startsWith(item.to));
            return (
              <NavLink
                key={item.label}
                to={item.to}
                className={cn(
                  "group flex items-center gap-md rounded-lg p-md transition-all duration-200",
                  active
                    ? "bg-primary-container/20 font-bold text-primary"
                    : "text-secondary hover:bg-surface-container-high hover:text-on-surface"
                )}
              >
                <Icon name={item.icon} filled={active}
                      className="transition-transform group-hover:scale-110" />
                <span className="font-label-md text-label-md">{item.label}</span>
              </NavLink>
            );
          })}

          <div className="mt-auto">
            <NavLink
              to="/profile/settings"
              className="group flex items-center gap-md rounded-lg p-md text-secondary transition-all duration-200 hover:bg-surface-container-high hover:text-on-surface"
            >
              <Icon name="settings" className="transition-transform group-hover:scale-110" />
              <span className="font-label-md text-label-md">Settings</span>
            </NavLink>
          </div>
        </nav>

        <NavLink
          to={fan ? "/profile" : "/login"}
          className="mt-lg rounded-xl border border-outline-variant/20 bg-surface-container/70 p-lg backdrop-blur transition-colors hover:border-primary/40"
        >
          <div className="flex items-center gap-md">
            <div className="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary-container font-bold text-on-primary-container">
              {fan?.avatar_url
                ? <img src={fan.avatar_url} alt={fan.name} className="h-full w-full object-cover" />
                : fan ? initials : <Icon name="person" filled />}
            </div>
            <div className="min-w-0">
              <p className="truncate font-label-md text-label-md leading-tight text-on-surface">
                {fan?.name ?? "Sign in"}
              </p>
              <p className="truncate font-label-sm text-label-sm text-secondary">
                {fan ? (fan.email ?? fan.phone) : "Access your fan account"}
              </p>
            </div>
          </div>
        </NavLink>
      </aside>

      {/* Main wrapper */}
      <main className="flex min-h-screen flex-col pb-24 md:ml-64 md:pb-0">
        {/* Top nav */}
        <header className="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-outline-variant/20 bg-surface-container px-lg">
          <div className="flex items-center gap-xl">
            <div className="relative w-48 lg:w-64">
              <Icon name="search"
                    className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-secondary text-[20px]" />
              <input
                type="text"
                placeholder="Search music…"
                className="w-full rounded-full border border-outline-variant/30 bg-background py-xs pl-10 pr-md font-body-md text-body-md outline-none transition-colors focus:border-primary"
              />
            </div>
            <nav className="hidden items-center gap-xl lg:flex">
              {topNav.map((link, i) => {
                const active = link.end
                  ? location.pathname === link.to
                  : location.pathname.startsWith(link.to);
                return (
                  <NavLink
                    key={`${link.label}-${i}`}
                    to={link.to}
                    end={link.end}
                    className={cn(
                      "font-body-md text-body-md transition-colors duration-200",
                      active
                        ? "border-b-2 border-primary pb-1 font-bold text-primary"
                        : "text-secondary hover:text-primary"
                    )}
                  >
                    {link.label}
                  </NavLink>
                );
              })}
            </nav>
          </div>

          <div className="flex items-center gap-md">
            <NavLink
              to={fan ? "/profile" : "/login"}
              className="flex items-center gap-sm rounded-full border border-outline-variant/30 bg-surface-container-high px-md py-xs transition-colors hover:border-primary/40"
            >
              <Icon name={fan ? "person" : "login"} className="text-secondary" />
              <span className="hidden font-label-md text-label-md sm:inline">{fan?.name ?? "Sign in"}</span>
            </NavLink>
          </div>
        </header>

        {/* Page slot */}
        <div className="mx-auto w-full max-w-[1400px] flex-grow p-xl">
          {children ?? <Outlet />}
        </div>

        {/* Footer */}
        <footer className="mt-auto flex w-full flex-col items-center justify-between gap-md border-t border-outline-variant/20 bg-surface-container-lowest px-lg py-xl md:flex-row">
          <div className="flex flex-col items-center gap-sm md:items-start">
            <span className="font-label-md text-label-md font-bold text-on-surface">SoundRedeem Music</span>
            <p className="font-label-sm text-label-sm text-secondary">© 2026 SoundRedeem Music. All rights reserved.</p>
          </div>
          <div className="flex gap-xl">
            {["Terms", "Privacy", "Artist Support", "Contact"].map((l) => (
              <a key={l} href="#" className="font-label-sm text-label-sm text-secondary transition-colors hover:text-primary">{l}</a>
            ))}
          </div>
        </footer>
      </main>

      <div className="md:hidden">
        <BottomNav />
      </div>
    </div>
  );
}
