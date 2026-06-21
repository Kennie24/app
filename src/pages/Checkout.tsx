import { useEffect, useMemo, useRef, useState } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import { motion, AnimatePresence } from "framer-motion";
import { Icon } from "@/components/Icon";
import { Reveal } from "@/components/Reveal";
import { FanShell } from "@/components/FanShell";
import { CountryPhoneInput } from "@/components/CountryPhoneInput";
import { catalogApi, type CatalogDetail } from "@/lib/catalogApi";
import { checkoutApi, type CheckoutMethod, type CheckoutPurchase } from "@/lib/checkoutApi";
import { fanApi } from "@/lib/fanApi";

type MethodOption = {
  id: CheckoutMethod;
  label: string;
  sub: string;
  shortLabel: string;
  icon?: string;
  image?: string;
  iconBg: string;
  iconColor: string;
};

const METHODS: MethodOption[] = [
  { id: "mtn_momo",     label: "MTN Mobile Money", shortLabel: "MTN Money",     sub: "Fast & Secure",       image: "/payment-logos/mtn-momo.avif",     iconBg: "bg-yellow-400",            iconColor: "text-black" },
  { id: "airtel_money", label: "Airtel Money",     shortLabel: "Airtel Money",  sub: "Instant Payment",     image: "/payment-logos/airtel-money.jpg", iconBg: "bg-red-600",               iconColor: "text-white" },
  { id: "card",         label: "Bank Card",        shortLabel: "Bank Card",     sub: "Visa, Mastercard",    icon: "credit_card",                      iconBg: "bg-surface-container-high", iconColor: "text-on-surface" },
  { id: "paypal",       label: "PayPal",           shortLabel: "PayPal",        sub: "Fast & Secure",       icon: "account_balance_wallet",           iconBg: "bg-surface-container-high", iconColor: "text-on-surface" },
];

const PLATFORM_FEE = 1.50;

export function Checkout() {
  const { key = "" } = useParams<{ key: string }>();
  const navigate = useNavigate();

  const [release, setRelease] = useState<CatalogDetail | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const [method, setMethod] = useState<CheckoutMethod>("card");
  const [msisdn, setMsisdn] = useState("");
  const [email, setEmail] = useState("");
  const [cardName, setCardName] = useState("");
  const [cardNumber, setCardNumber] = useState("");
  const [cardExpiry, setCardExpiry] = useState("");
  const [cardCvv, setCardCvv] = useState("");

  const [purchase, setPurchase] = useState<CheckoutPurchase | null>(null);
  const [paying, setPaying] = useState(false);
  const [sandbox, setSandbox] = useState(false);
  const pollRef = useRef<number | null>(null);

  useEffect(() => {
    catalogApi.show(key)
      .then((res) => setRelease(res.release))
      .catch((reason) => setError(reason instanceof Error ? reason.message : "Could not load this release."))
      .finally(() => setLoading(false));

    fanApi.me().then(({ user }) => {
      if (user.phone) setMsisdn(user.phone);
      if (user.email) setEmail(user.email);
      if (user.name) setCardName(user.name);
    }).catch(() => {/* guest */});
  }, [key]);

  useEffect(() => () => { if (pollRef.current) window.clearTimeout(pollRef.current); }, []);

  const needsMsisdn = method === "mtn_momo" || method === "airtel_money";
  const needsCard   = method === "card";
  const needsEmail  = method === "paypal";

  const subtotal = Number(release?.price ?? 0);
  const tax = 0;
  const total = (subtotal + PLATFORM_FEE + tax).toFixed(2);
  const trackCount = release?.tracks.length ?? 0;
  const type = release?.type ?? "Album";

  const canPay = useMemo(() => {
    if (!release || paying) return false;
    if (needsMsisdn && !/^\+[1-9]\d{7,14}$/.test(msisdn)) return false;
    if (needsEmail  && !/.+@.+\..+/.test(email)) return false;
    if (needsCard) {
      const num = cardNumber.replace(/\s+/g, "");
      if (!cardName.trim()) return false;
      if (!/^\d{13,19}$/.test(num)) return false;
      if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) return false;
      if (!/^\d{3,4}$/.test(cardCvv)) return false;
    }
    return true;
  }, [release, paying, needsMsisdn, needsEmail, needsCard, msisdn, email, cardName, cardNumber, cardExpiry, cardCvv]);

  async function pollStatus(reference: string) {
    try {
      const { purchase: p } = await checkoutApi.status(reference);
      setPurchase(p);
      if (p.status === "succeeded" || p.status === "failed" || p.status === "cancelled") return;
      pollRef.current = window.setTimeout(() => pollStatus(reference), 2000);
    } catch {
      pollRef.current = window.setTimeout(() => pollStatus(reference), 3000);
    }
  }

  async function pay() {
    if (!release || !canPay) return;
    setPaying(true);
    setError("");
    try {
      const res = await checkoutApi.initiate({
        asset_key: release.slug || release.id,
        method,
        msisdn: needsMsisdn ? msisdn : undefined,
        email: needsEmail || needsCard ? (email || undefined) : undefined,
      });
      setPurchase(res.purchase);
      setSandbox(res.sandbox);
      pollStatus(res.purchase.reference);
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Payment could not be initiated.");
      setPaying(false);
    }
  }

  /* ────────────────────────────  layout helpers ──────────────────────────── */

  const Content = (
    <>
      {/* MOBILE-ONLY header bar (desktop uses the FanShell topnav) */}
      <header className="sticky top-0 z-30 -mx-xl mb-md flex h-14 items-center gap-md border-b border-outline-variant/20 bg-background/85 px-xl backdrop-blur lg:hidden">
        <button onClick={() => navigate(-1)} className="-ml-sm flex h-10 w-10 items-center justify-center rounded-full text-on-surface hover:bg-surface-container-high" aria-label="Back">
          <Icon name="arrow_back" />
        </button>
        <h1 className="font-headline-md text-headline-md font-extrabold text-primary">Checkout</h1>
      </header>

      <div className="grid grid-cols-1 gap-lg lg:grid-cols-12">
        {/* ─── LEFT COLUMN ─── Order summary */}
        <section className="lg:col-span-5">
          <Reveal direction="up">
            <div className="mb-md flex items-center gap-sm">
              <button onClick={() => navigate(-1)} className="hidden h-9 w-9 items-center justify-center rounded-full text-on-surface hover:bg-surface-container-high lg:flex" aria-label="Back">
                <Icon name="arrow_back" />
              </button>
              <h2 className="font-headline-md text-headline-md font-bold">Order Summary</h2>
            </div>

            <div className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-md md:p-lg">
              <div className="flex items-start gap-md">
                <div className="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-surface-container md:h-24 md:w-24">
                  {release?.image
                    ? <img src={release.image} alt={release.title} className="h-full w-full object-cover" />
                    : <div className="flex h-full w-full items-center justify-center"><Icon name="album" className="text-secondary text-[36px]" /></div>}
                </div>
                <div className="min-w-0 flex-1">
                  <h3 className="truncate font-headline-md text-headline-md font-bold text-primary">{release?.title}</h3>
                  <p className="truncate font-body-md text-body-md text-secondary">{release?.artist}</p>
                  <span className="mt-xs inline-block rounded-full bg-primary/15 px-sm py-[2px] font-label-sm text-label-sm font-bold uppercase tracking-widest text-primary">
                    {type === "Album" ? "Full Album" : "Single"} {trackCount > 0 && <>· {trackCount} {trackCount === 1 ? "Track" : "Tracks"}</>}
                  </span>
                  <p className="mt-xs font-headline-md text-headline-md font-extrabold text-on-surface">${subtotal.toFixed(2)}</p>
                </div>
              </div>

              <div className="my-md border-t border-outline-variant/20" />

              <dl className="space-y-xs font-body-md text-body-md">
                <Row label="Subtotal"     value={`$${subtotal.toFixed(2)}`} />
                <Row label="Platform Fee" value={`$${PLATFORM_FEE.toFixed(2)}`} />
                <Row label="Tax"          value={`$${tax.toFixed(2)}`} />
              </dl>

              <div className="my-md border-t border-outline-variant/20" />

              <div className="flex items-center justify-between">
                <span className="font-headline-md text-headline-md font-bold">Total Amount</span>
                <span className="font-headline-md text-headline-md font-extrabold text-primary">${total}</span>
              </div>
            </div>
          </Reveal>

          <Reveal direction="up" delay={0.1}>
            <div className="mt-md flex items-center gap-md rounded-2xl border border-outline-variant/20 bg-surface-container-low p-md">
              <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary">
                <Icon name="verified_user" filled />
              </div>
              <div>
                <p className="font-body-md text-body-md font-bold text-on-surface">Secure Checkout</p>
                <p className="font-label-sm text-label-sm text-secondary">256-bit SSL encrypted payment processing.</p>
              </div>
            </div>
          </Reveal>
        </section>

        {/* ─── RIGHT COLUMN ─── Payment method */}
        <section className="lg:col-span-7">
          <Reveal direction="up" delay={0.05}>
            <h2 className="mb-md font-headline-md text-headline-md font-bold">Payment Method</h2>

            <div className="grid grid-cols-2 gap-sm md:grid-cols-4">
              {METHODS.map((m) => {
                const active = method === m.id;
                return (
                  <motion.button
                    key={m.id}
                    type="button"
                    onClick={() => setMethod(m.id)}
                    whileTap={{ scale: 0.97 }}
                    className={`flex flex-col items-center gap-sm rounded-2xl border-2 p-md text-center transition-colors ${
                      active ? "border-primary bg-primary/5" : "border-outline-variant/20 bg-surface-container-low hover:border-outline-variant/40"
                    }`}
                  >
                    <span className={`flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg ${m.iconBg}`}>
                      {m.image ? (
                        <img src={m.image} alt={m.label} className="h-full w-full object-cover" />
                      ) : (
                        <Icon name={m.icon ?? ""} className={`${m.iconColor} text-[22px]`} filled={active} />
                      )}
                    </span>
                    <span className="font-label-md text-label-md font-bold text-on-surface">{m.shortLabel}</span>
                  </motion.button>
                );
              })}
            </div>
          </Reveal>

          <Reveal direction="up" delay={0.1}>
            <div className="mt-md rounded-2xl border border-outline-variant/20 bg-surface-container-low p-md md:p-lg">
              <AnimatePresence mode="wait">
                {needsCard && (
                  <motion.div key="card" initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -10 }} className="space-y-md">
                    <FieldLabel>Cardholder Name</FieldLabel>
                    <input
                      value={cardName}
                      onChange={(e) => setCardName(e.target.value)}
                      placeholder="John Doe"
                      autoComplete="cc-name"
                      className="w-full rounded-xl border-2 border-outline-variant/20 bg-surface-container-high px-md py-md font-body-md text-body-md outline-none transition-colors focus:border-primary"
                    />

                    <FieldLabel>Card Number</FieldLabel>
                    <div className="relative">
                      <Icon name="credit_card" className="pointer-events-none absolute left-md top-1/2 -translate-y-1/2 text-secondary text-[20px]" />
                      <input
                        inputMode="numeric"
                        value={cardNumber}
                        onChange={(e) => setCardNumber(formatCardNumber(e.target.value))}
                        placeholder="•••• •••• •••• ••••"
                        autoComplete="cc-number"
                        className="w-full rounded-xl border-2 border-outline-variant/20 bg-surface-container-high px-md py-md pl-12 font-body-md text-body-md outline-none transition-colors focus:border-primary"
                      />
                    </div>

                    <div className="grid grid-cols-2 gap-md">
                      <div>
                        <FieldLabel>Expiry Date</FieldLabel>
                        <input
                          inputMode="numeric"
                          value={cardExpiry}
                          onChange={(e) => setCardExpiry(formatExpiry(e.target.value))}
                          placeholder="MM/YY"
                          autoComplete="cc-exp"
                          className="w-full rounded-xl border-2 border-outline-variant/20 bg-surface-container-high px-md py-md font-body-md text-body-md outline-none transition-colors focus:border-primary"
                        />
                      </div>
                      <div>
                        <FieldLabel>CVV</FieldLabel>
                        <input
                          inputMode="numeric"
                          value={cardCvv}
                          onChange={(e) => setCardCvv(e.target.value.replace(/\D/g, "").slice(0, 4))}
                          placeholder="•••"
                          autoComplete="cc-csc"
                          className="w-full rounded-xl border-2 border-outline-variant/20 bg-surface-container-high px-md py-md font-body-md text-body-md outline-none transition-colors focus:border-primary"
                        />
                      </div>
                    </div>
                  </motion.div>
                )}

                {needsMsisdn && (
                  <motion.div key="msisdn" initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -10 }}>
                    <FieldLabel>Mobile money number</FieldLabel>
                    <CountryPhoneInput value={msisdn} onChange={setMsisdn} required />
                    <p className="mt-xs text-center font-label-sm text-label-sm text-secondary">
                      You will receive a prompt on your phone to approve the transaction.
                    </p>
                  </motion.div>
                )}

                {needsEmail && (
                  <motion.div key="email" initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -10 }}>
                    <FieldLabel>PayPal email</FieldLabel>
                    <input
                      type="email"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="you@example.com"
                      className="w-full rounded-xl border-2 border-outline-variant/20 bg-surface-container-high px-md py-md font-body-md text-body-md outline-none transition-colors focus:border-primary"
                    />
                  </motion.div>
                )}
              </AnimatePresence>
            </div>
          </Reveal>

          {error && (
            <div role="alert" className="mt-md flex items-center gap-sm rounded-xl border border-error/40 bg-error-container/20 p-md text-error">
              <Icon name="error" /><span className="text-body-md">{error}</span>
            </div>
          )}

          <Reveal direction="up" delay={0.15}>
            <motion.button
              whileTap={{ scale: 0.98 }}
              disabled={!canPay}
              onClick={() => void pay()}
              className="mt-md flex w-full items-center justify-center gap-sm rounded-full bg-primary py-md font-headline-md text-headline-md font-bold text-on-primary shadow-[0_12px_30px_-12px_rgba(29,185,84,0.65)] transition-all disabled:cursor-not-allowed disabled:opacity-50 hover:brightness-110"
            >
              {paying
                ? <><Icon name="progress_activity" className="animate-spin" /> Processing…</>
                : <>Confirm & Pay ${total} <Icon name="arrow_forward" /></>
              }
            </motion.button>
            <p className="mt-sm text-center font-label-sm text-label-sm text-secondary">
              By clicking "Confirm & Pay", you agree to SoundRedeem's{" "}
              <Link to="#" className="font-bold text-on-surface hover:underline">Terms of Service</Link> and{" "}
              <Link to="#" className="font-bold text-on-surface hover:underline">Refund Policy</Link>.
            </p>
          </Reveal>
        </section>
      </div>
    </>
  );

  /* ───────────────────────────  status overlay ─────────────────────────── */
  const Status = purchase ? (
    <StatusView purchase={purchase} release={release!} sandbox={sandbox}
                onClose={() => { setPurchase(null); setPaying(false); }}
                onDone={() => navigate(`/store/${release!.slug || release!.id}`)} />
  ) : null;

  /* ───────────────────────────  render strategy ─────────────────────────── */

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

  return <Wrapper>{Status ?? Content}</Wrapper>;
}

/* ─── Layout: FanShell on desktop, minimal on mobile ─── */
function Wrapper({ children }: { children: React.ReactNode }) {
  return (
    <>
      {/* Mobile (single column, no FanShell chrome) */}
      <div className="lg:hidden min-h-screen bg-background text-on-background">
        <main className="mx-auto w-full max-w-md px-xl pb-xl pt-md">{children}</main>
      </div>
      {/* Desktop: full FanShell with two-column body */}
      <div className="hidden lg:block">
        <FanShell>{children}</FanShell>
      </div>
    </>
  );
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex justify-between">
      <dt className="text-secondary">{label}</dt>
      <dd className="text-on-surface">{value}</dd>
    </div>
  );
}

function FieldLabel({ children }: { children: React.ReactNode }) {
  return <label className="mb-xs block font-label-md text-label-md font-bold text-secondary">{children}</label>;
}

function formatCardNumber(raw: string) {
  return raw.replace(/\D/g, "").slice(0, 19).replace(/(\d{4})(?=\d)/g, "$1 ");
}

function formatExpiry(raw: string) {
  const d = raw.replace(/\D/g, "").slice(0, 4);
  if (d.length < 3) return d;
  return `${d.slice(0, 2)}/${d.slice(2)}`;
}

function StatusView({
  purchase, release, sandbox, onClose, onDone,
}: {
  purchase: CheckoutPurchase;
  release: CatalogDetail;
  sandbox: boolean;
  onClose: () => void;
  onDone: () => void;
}) {
  const final = purchase.status === "succeeded" || purchase.status === "failed" || purchase.status === "cancelled";
  const labels = {
    pending:    { icon: "hourglass_top",     text: "Awaiting confirmation",   tone: "text-primary" },
    processing: { icon: "progress_activity", text: "Confirm on your device",  tone: "text-primary" },
    succeeded:  { icon: "check_circle",      text: "Payment received",        tone: "text-primary" },
    failed:     { icon: "error",             text: "Payment failed",          tone: "text-error" },
    cancelled:  { icon: "cancel",            text: "Payment cancelled",       tone: "text-secondary" },
  }[purchase.status];

  return (
    <motion.div initial={{ opacity: 0, scale: 0.96 }} animate={{ opacity: 1, scale: 1 }}
                className="mx-auto mt-lg max-w-md rounded-2xl border border-outline-variant/20 bg-surface-container-low p-xl text-center">
      <div className={`mx-auto mb-md flex h-20 w-20 items-center justify-center rounded-full bg-primary/15 ${labels.tone}`}>
        <Icon name={labels.icon} filled className={`text-[44px] ${purchase.status === "processing" ? "animate-spin" : ""}`} />
      </div>
      <h2 className={`font-headline-md text-headline-md font-bold ${labels.tone}`}>{labels.text}</h2>
      <p className="mt-xs font-body-md text-body-md text-secondary">
        {release.title} · ${purchase.amount} {purchase.currency}
      </p>
      <p className="mt-xs font-label-sm text-label-sm text-secondary">Ref: {purchase.reference}</p>
      {sandbox && (
        <p className="mt-md inline-block rounded-full bg-primary/15 px-md py-xs font-label-sm text-label-sm font-bold uppercase tracking-widest text-primary">
          Sandbox mode
        </p>
      )}
      {purchase.failure_reason && <p className="mt-md text-body-md text-error">{purchase.failure_reason}</p>}

      {final ? (
        <button onClick={onDone}
                className="mt-xl w-full rounded-full bg-primary py-md font-bold text-on-primary hover:brightness-110">
          {purchase.status === "succeeded" ? "Continue" : "Try again"}
        </button>
      ) : (
        <button onClick={onClose}
                className="mt-xl inline-flex items-center gap-sm rounded-full border border-outline-variant/30 px-lg py-sm font-label-md text-label-md text-secondary hover:border-primary/40 hover:text-primary">
          Cancel
        </button>
      )}
    </motion.div>
  );
}
