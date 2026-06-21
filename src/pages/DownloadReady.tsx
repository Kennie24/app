import { Link } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { Reveal } from "@/components/Reveal";
import { Button } from "@/components/ui/button";

export function DownloadReady() {
  return (
    <main className="flex min-h-screen items-center justify-center px-container-margin pb-32 pt-28">
      <Reveal direction="scale" className="w-full max-w-md">
        <section className="rounded-2xl border border-dashed border-outline-variant/30 bg-surface-container-low p-xl text-center">
          <Icon name="download" className="mx-auto text-[56px] text-secondary" />
          <h1 className="mt-lg font-headline-lg text-headline-lg">No download available</h1>
          <p className="mt-sm text-body-lg text-secondary">
            A real download will appear only after a verified redemption provides downloadable files.
          </p>
          <Button asChild className="mt-lg">
            <Link to="/scan">Scan a redemption code</Link>
          </Button>
        </section>
      </Reveal>
    </main>
  );
}
