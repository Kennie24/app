import { Link } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { Reveal } from "@/components/Reveal";

export function AdminDashboard() {
  return (
    <main className="mx-auto max-w-4xl px-container-margin py-24">
      <Reveal as="section" direction="down" className="rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-xl text-center">
        <div className="mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary/15 text-primary">
          <Icon name="security" className="text-[36px]" />
        </div>
        <h1 className="font-headline-lg text-headline-lg font-black text-on-surface">Admin dashboard</h1>
        <p className="mx-auto mt-sm max-w-xl font-body-md text-body-md text-secondary">
          Mock redemption metrics have been removed. Use the super-admin dashboard for live platform data.
        </p>
        <div className="mt-lg flex flex-col items-center justify-center gap-md sm:flex-row">
          <a
            href="/super-admin"
            className="inline-flex items-center gap-sm rounded-full bg-primary-container px-lg py-sm font-label-md text-label-md font-bold text-on-primary-container transition-colors hover:brightness-110"
          >
            <Icon name="arrow_forward" />
            Open super admin
          </a>
          <Link
            to="/store"
            className="inline-flex items-center gap-sm rounded-full border border-outline-variant/30 px-lg py-sm font-label-md text-label-md text-on-surface transition-colors hover:border-primary/40 hover:text-primary"
          >
            <Icon name="library_music" />
            Back to store
          </Link>
        </div>
      </Reveal>
    </main>
  );
}
