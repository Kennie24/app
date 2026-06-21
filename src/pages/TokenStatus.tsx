import { Link } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { Reveal } from "@/components/Reveal";
import { Button } from "@/components/ui/button";

export function TokenStatus() {
  return (
    <main className="flex min-h-screen items-center justify-center px-container-margin pb-32 pt-28">
      <Reveal direction="scale" className="w-full max-w-md">
        <section className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-xl text-center">
          <Icon name="confirmation_number" className="mx-auto text-[56px] text-secondary" />
          <h1 className="mt-lg font-headline-lg text-headline-lg">No token selected</h1>
          <p className="mt-sm text-body-lg text-secondary">
            Token status will appear after a code is checked by the redemption service.
          </p>
          <Button asChild className="mt-lg">
            <Link to="/scan">Scan or enter a code</Link>
          </Button>
        </section>
      </Reveal>
    </main>
  );
}
