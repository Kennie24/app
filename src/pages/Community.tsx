import { Link } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { FanShell } from "@/components/FanShell";
import { Reveal } from "@/components/Reveal";

export function Community() {
  return (
    <FanShell>
      <Reveal direction="down">
        <header className="mb-xl">
          <p className="font-label-md text-label-md uppercase tracking-widest text-primary">Community</p>
          <h2 className="mt-xs font-headline-lg text-headline-lg font-bold">Fan space</h2>
          <p className="mt-xs font-body-md text-body-md text-secondary">
            Real fan conversations will appear here after the community backend is connected.
          </p>
        </header>
      </Reveal>

      <Reveal direction="up">
        <section className="rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-xl text-center">
          <div className="mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary/15 text-primary">
            <Icon name="forum" className="text-[36px]" />
          </div>
          <h3 className="font-headline-md text-headline-md font-bold">No community activity yet</h3>
          <p className="mt-sm mx-auto max-w-md font-body-md text-body-md text-secondary">
            This page will stay empty until real community data is available.
          </p>
          <div className="mt-lg flex flex-col items-center justify-center gap-md sm:flex-row">
            <Link
              to="/discover"
              className="inline-flex items-center gap-sm rounded-full bg-primary-container px-lg py-sm font-label-md text-label-md font-bold text-on-primary-container transition-colors hover:brightness-110"
            >
              <Icon name="explore" /> Discover releases
            </Link>
            <Link
              to="/artists"
              className="inline-flex items-center gap-sm rounded-full border border-outline-variant/30 px-lg py-sm font-label-md text-label-md text-on-surface transition-colors hover:border-primary/40 hover:text-primary"
            >
              <Icon name="person" /> Browse artists
            </Link>
          </div>
        </section>
      </Reveal>
    </FanShell>
  );
}
