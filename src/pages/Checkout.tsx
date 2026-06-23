import { useEffect, useState } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import { motion } from "framer-motion";
import { Icon } from "@/components/Icon";
import { Reveal } from "@/components/Reveal";
import { FanShell } from "@/components/FanShell";
import { catalogApi, type CatalogDetail } from "@/lib/catalogApi";
import { checkoutApi } from "@/lib/checkoutApi";
import { fanApi } from "@/lib/fanApi";

const PLATFORM_FEE = 0;

export function Checkout() {
  const { key = "" } = useParams<{ key: string }>();
  const navigate = useNavigate();

  const [release, setRelease] = useState<CatalogDetail | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [email, setEmail] = useState("");
  const [paying, setPaying] = useState(false);

  useEffect(() => {
    catalogApi.show(key)
      .then((res) => setRelease(res.release))
      .catch((reason) => setError(reason instanceof Error ? reason.message : "Could not load this release."))
      .finally(() => setLoading(false));

    fanApi.me().then(({ user }) => {
      if (user.email) setEmail(user.email);
    }).catch(() => {/* guest */});
  }, [key]);

  const subtotal = Number(release?.price ?? 0);
  const total = (subtotal + PLATFORM_FEE).toFixed(2);
  const trackCount = release?.tracks.length ?? 0;
  const type = release?.type ?? "Album";
  const purchaseLabel = type === "Album" ? "Full Album" : type === "EP" ? "Full EP" : "Single";

  async function pay() {
    if (!release || paying) return;
    setPaying(true);
    setError("");
    try {
      const res = await checkoutApi.initiate({
        asset_key: release.slug || release.id,
        email: email || undefined,
      });
      // Redirect to PesaPal hosted payment page
      window.location.href = res.redirect_url;
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Payment could not be initiated.");
      setPaying(false);
    }
  }

  const Content = (
    <>
      {/* Mobile header */}
      <header className="sticky top-0 z-30 -mx-xl mb-md flex h-14 items-center gap-md border-b border-outline-variant/20 bg-background/85 px-xl backdrop-blur lg:hidden">
        <button onClick={() => navigate(-1)} className="-ml-sm flex h-10 w-10 items-center justify-center rounded-full text-on-surface hover:bg-surface-container-high" aria-label="Back">
          <Icon name="arrow_back" />
        </button>
        <h1 className="font-headline-md text-headline-md font-extrabold text-primary">Checkout</h1>
      </header>

      <div className="mx-auto max-w-md space-y-lg">
        {/* Order summary */}
        <Reveal direction="up">
          <div className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-lg">
            <div className="mb-md hidden items-center gap-sm lg:flex">
              <button onClick={() => navigate(-1)} className="flex h-9 w-9 items-center justify-center rounded-full text-on-surface hover:bg-surface-container-high" aria-label="Back">
                <Icon name="arrow_back" />
              </button>
              <h2 className="font-headline-md text-headline-md font-bold">Order Summary</h2>
            </div>

            <div className="flex items-start gap-md">
              <div className="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-surface-container">
                {release?.image
                  ? <img src={release.image} alt={release.title} className="h-full w-full object-cover" />
                  : <div className="flex h-full w-full items-center justify-center"><Icon name="album" className="text-secondary text-[36px]" /></div>}
              </div>
              <div className="min-w-0 flex-1">
                <h3 className="truncate font-headline-md text-headline-md font-bold text-primary">{release?.title}</h3>
                <p className="truncate font-body-md text-body-md text-secondary">{release?.artist}</p>
                <span className="mt-xs inline-block rounded-full bg-primary/15 px-sm py-[2px] font-label-sm text-label-sm font-bold uppercase tracking-widest text-primary">
                  {purchaseLabel}{trackCount > 0 && <> · {trackCount} {trackCount === 1 ? "Track" : "Tracks"}</>}
                </span>
                <p className="mt-xs font-headline-md text-headline-md font-extrabold text-on-surface">UGX {subtotal.toFixed(2)}</p>
              </div>
            </div>

            <div className="my-md border-t border-outline-variant/20" />

            <dl className="space-y-xs font-body-md text-body-md">
              <div className="flex justify-between">
                <dt className="text-secondary">Subtotal</dt>
                <dd className="text-on-surface">UGX {subtotal.toFixed(2)}</dd>
              </div>
            </dl>

            <div className="my-md border-t border-outline-variant/20" />

            <div className="flex items-center justify-between">
              <span className="font-headline-md text-headline-md font-bold">Total</span>
              <span className="font-headline-md text-headline-md font-extrabold text-primary">UGX {total}</span>
            </div>
          </div>
        </Reveal>

        {/* Email field */}
        <Reveal direction="up" delay={0.05}>
          <div className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-lg">
            <label className="mb-xs block font-label-md text-label-md font-bold text-secondary">
              Email (for receipt)
            </label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="you@example.com"
              className="w-full rounded-xl border-2 border-outline-variant/20 bg-surface-container-high px-md py-md font-body-md text-body-md outline-none transition-colors focus:border-primary"
            />
          </div>
        </Reveal>

        {/* PesaPal payment button */}
        <Reveal direction="up" delay={0.1}>
          <div className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-lg">
            <div className="mb-md flex items-center gap-md">
              <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10">
                <Icon name="payment" className="text-primary text-[26px]" filled />
              </div>
              <div>
                <p className="font-body-md text-body-md font-bold text-on-surface">Pay with PesaPal</p>
                <p className="font-label-sm text-label-sm text-secondary">Cards, Mobile Money, and more</p>
              </div>
            </div>

            {error && (
              <div role="alert" className="mb-md flex items-center gap-sm rounded-xl border border-error/40 bg-error-container/20 p-md text-error">
                <Icon name="error" /><span className="text-body-md">{error}</span>
              </div>
            )}

            <motion.button
              whileTap={{ scale: 0.98 }}
              disabled={!release || paying}
              onClick={() => void pay()}
              className="flex w-full items-center justify-center gap-sm rounded-full bg-primary py-md font-headline-md text-headline-md font-bold text-on-primary shadow-[0_12px_30px_-12px_rgba(29,185,84,0.65)] transition-all disabled:cursor-not-allowed disabled:opacity-50 hover:brightness-110"
            >
              {paying
                ? <><Icon name="progress_activity" className="animate-spin" /> Redirecting…</>
                : <>Pay UGX {total} <Icon name="arrow_forward" /></>
              }
            </motion.button>
          </div>
        </Reveal>

        <Reveal direction="up" delay={0.15}>
          <div className="flex items-center gap-md rounded-2xl border border-outline-variant/20 bg-surface-container-low p-md">
            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary">
              <Icon name="verified_user" filled />
            </div>
            <div>
              <p className="font-body-md text-body-md font-bold text-on-surface">Secure Checkout</p>
              <p className="font-label-sm text-label-sm text-secondary">
                Powered by PesaPal · 256-bit SSL encrypted.{" "}
                <Link to="#" className="font-bold text-on-surface hover:underline">Terms</Link>
              </p>
            </div>
          </div>
        </Reveal>
      </div>
    </>
  );

  if (loading) {
    return (
      <Wrapper>
        <div className="flex min-h-[60vh] items-center justify-center">
          <Icon name="progress_activity" className="animate-spin text-primary text-[42px]" />
        </div>
      </Wrapper>
    );
  }

  if (error && !release) {
    return (
      <Wrapper>
        <div className="mx-auto max-w-md rounded-2xl border border-error/30 bg-error-container/20 p-xl text-center text-error">
          <Icon name="error" className="text-[36px]" />
          <p className="mt-md font-body-md">{error || "Release not found."}</p>
          <Link to="/store" className="mt-md inline-block text-primary hover:underline">← Back to store</Link>
        </div>
      </Wrapper>
    );
  }

  return <Wrapper>{Content}</Wrapper>;
}

function Wrapper({ children }: { children: React.ReactNode }) {
  return (
    <>
      <div className="lg:hidden min-h-screen bg-background text-on-background">
        <main className="mx-auto w-full max-w-md px-xl pb-xl pt-md">{children}</main>
      </div>
      <div className="hidden lg:block">
        <FanShell>{children}</FanShell>
      </div>
    </>
  );
}
