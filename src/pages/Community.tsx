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
            A home for fan-to-fan and fan-to-artist conversation around verified releases.
          </p>
        </header>
      </Reveal>

      <Reveal direction="up">
        <section className="rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-xl text-center">
          <div className="mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary/15 text-primary">
            <Icon name="forum" className="text-[36px]" />
          </div>
          <h3 className="font-headline-md text-headline-md font-bold">Community is coming soon</h3>
          <p className="mt-sm mx-auto max-w-md font-body-md text-body-md text-secondary">
            We're building artist Q&amp;A threads, listening parties, and verified-redeemer-only chats.
            Nothing is here yet because we won't fake activity — once the backend ships, real
            conversations will show up in this space.
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

      <Reveal direction="up" delay={0.1}>
        <section className="mt-xl grid grid-cols-1 gap-lg sm:grid-cols-3">
          {[
            { icon: "verified", title: "Verified members", text: "Only fans with at least one redeemed asset can post." },
            { icon: "music_note", title: "Release threads", text: "Each live release will get its own thread, pinned by the artist." },
            { icon: "calendar_month", title: "Listening parties", text: "Synced previews and live artist drop-ins around launch days." },
          ].map((card) => (
            <div key={card.title} className="rounded-xl border border-outline-variant/20 bg-surface-container-low p-lg">
              <div className="mb-sm flex h-10 w-10 items-center justify-center rounded-full bg-primary/15 text-primary">
                <Icon name={card.icon} />
              </div>
              <h4 className="font-body-lg text-body-lg font-bold text-on-surface">{card.title}</h4>
              <p className="mt-xs font-body-md text-body-md text-secondary">{card.text}</p>
            </div>
          ))}
        </section>
      </Reveal>
    </FanShell>
  );
}
