import { useState, type FormEvent } from "react";
import { useSearchParams } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Reveal } from "@/components/Reveal";

export function RedeemYourEP() {
  const [searchParams] = useSearchParams();
  const [code, setCode] = useState(searchParams.get("code") ?? "");
  const [message, setMessage] = useState("");

  const submit = (event: FormEvent) => {
    event.preventDefault();
    if (!code.trim()) return;
    setMessage("Redemption verification is not connected yet. No content has been unlocked.");
  };

  return (
    <main className="min-h-screen px-container-margin pb-32 pt-28">
      <Reveal direction="up" className="mx-auto max-w-xl">
        <section className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-xl text-center">
          <div className="mx-auto mb-lg flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary">
            <Icon name="confirmation_number" className="text-[34px]" />
          </div>
          <h1 className="font-headline-lg text-headline-lg">Redeem music</h1>
          <p className="mx-auto mt-sm max-w-md text-body-lg text-secondary">
            Scan a valid SoundRedeem code or enter it below. Release details will appear only after
            the code is verified by the redemption service.
          </p>

          <form onSubmit={submit} className="mt-xl space-y-md text-left">
            <label className="block">
              <span className="mb-xs block font-label-md text-label-md uppercase tracking-widest text-secondary">
                Redemption code
              </span>
              <Input
                value={code}
                onChange={(event) => {
                  setCode(event.target.value);
                  setMessage("");
                }}
                placeholder="Enter your code"
                autoComplete="off"
              />
            </label>
            <Button type="submit" className="w-full" disabled={!code.trim()}>
              Verify code
            </Button>
          </form>

          {message && (
            <div role="status" className="mt-lg rounded-xl border border-outline-variant/30 bg-background/40 p-md text-body-md text-secondary">
              {message}
            </div>
          )}
        </section>

        <section className="mt-lg rounded-2xl border border-dashed border-outline-variant/30 p-xl text-center">
          <Icon name="library_music" className="mx-auto text-[42px] text-secondary" />
          <h2 className="mt-sm font-headline-md text-headline-md">Your library is empty</h2>
          <p className="mt-xs text-body-md text-secondary">
            Verified redemptions will appear here.
          </p>
        </section>
      </Reveal>
    </main>
  );
}
