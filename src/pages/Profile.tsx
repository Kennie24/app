import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { Reveal } from "@/components/Reveal";
import { Button } from "@/components/ui/button";
import { fanApi, type FanUser } from "@/lib/fanApi";

export function Profile() {
  const [user, setUser] = useState<FanUser | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fanApi.me()
      .then(({ user: loadedUser }) => setUser(loadedUser))
      .catch(() => setUser(null))
      .finally(() => setLoading(false));
  }, []);

  const initials = user?.name
    .split(/\s+/)
    .map((part) => part[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();

  return (
    <main className="mx-auto max-w-3xl px-container-margin pb-32 pt-28">
      <Reveal direction="up">
        <section className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-xl text-center">
          {loading ? (
            <Icon name="progress_activity" className="mx-auto animate-spin text-[42px] text-primary" />
          ) : user ? (
            <>
              <div className="mx-auto flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border-2 border-outline-variant bg-surface-container-high text-2xl font-bold text-primary">
                {user.avatar_url
                  ? <img src={user.avatar_url} alt={user.name} className="h-full w-full object-cover" />
                  : initials}
              </div>
              <h1 className="mt-lg font-headline-lg text-headline-lg">{user.name}</h1>
              <p className="mt-xs text-body-md text-secondary">{user.email ?? user.phone}</p>
              <Button asChild className="mt-lg">
                <Link to="/profile/settings">
                  <Icon name="edit" />
                  Edit profile
                </Link>
              </Button>
            </>
          ) : (
            <>
              <Icon name="person" className="mx-auto text-[48px] text-secondary" />
              <h1 className="mt-lg font-headline-lg text-headline-lg">Sign in to view your profile</h1>
              <Button asChild className="mt-lg">
                <Link to="/login">Go to fan login</Link>
              </Button>
            </>
          )}
        </section>

        <div className="mt-lg grid grid-cols-1 gap-md sm:grid-cols-2">
          <EmptyCard icon="library_music" title="No library items" text="Verified redemptions will appear here." />
          <EmptyCard icon="history" title="No redemption history" text="Completed fan activity will appear here." />
        </div>
      </Reveal>
    </main>
  );
}

function EmptyCard({ icon, title, text }: { icon: string; title: string; text: string }) {
  return (
    <section className="rounded-xl border border-dashed border-outline-variant/30 p-lg">
      <Icon name={icon} className="text-[32px] text-secondary" />
      <h2 className="mt-sm font-body-lg text-body-lg font-bold">{title}</h2>
      <p className="mt-xs text-body-md text-secondary">{text}</p>
    </section>
  );
}
