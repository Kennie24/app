import { useEffect, useRef, useState } from "react";
import { useNavigate, useSearchParams } from "react-router-dom";
import { motion } from "framer-motion";
import { Icon } from "@/components/Icon";
import { checkoutApi, type CheckoutPurchase } from "@/lib/checkoutApi";

export function CheckoutCallback() {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();

  const reference = searchParams.get("OrderMerchantReference");
  const trackingId = searchParams.get("OrderTrackingId");

  const [purchase, setPurchase] = useState<CheckoutPurchase | null>(null);
  const [error, setError] = useState("");
  const pollRef = useRef<number | null>(null);

  useEffect(() => {
    if (!reference) {
      setError("Invalid payment callback. No order reference found.");
      return;
    }
    poll(reference);
    return () => { if (pollRef.current) window.clearTimeout(pollRef.current); };
  }, [reference]);

  async function poll(ref: string) {
    try {
      const { purchase: p } = await checkoutApi.status(ref);
      setPurchase(p);
      if (p.status === "succeeded" || p.status === "failed" || p.status === "cancelled") return;
      pollRef.current = window.setTimeout(() => poll(ref), 2500);
    } catch {
      pollRef.current = window.setTimeout(() => poll(ref), 3000);
    }
  }

  const status = purchase?.status ?? "processing";

  const ui = {
    pending:    { icon: "hourglass_top",     text: "Awaiting confirmation",  sub: "Checking with PesaPal…",   tone: "text-primary",   spin: false },
    processing: { icon: "progress_activity", text: "Processing payment",     sub: "Please wait a moment…",    tone: "text-primary",   spin: true  },
    succeeded:  { icon: "check_circle",      text: "Payment successful!",    sub: "Thank you for your order.", tone: "text-primary",   spin: false },
    failed:     { icon: "error",             text: "Payment failed",         sub: "Your payment did not go through.", tone: "text-error",    spin: false },
    cancelled:  { icon: "cancel",            text: "Payment cancelled",      sub: "You cancelled the payment.", tone: "text-secondary", spin: false },
  }[status] ?? { icon: "progress_activity", text: "Checking status…", sub: "", tone: "text-primary", spin: true };

  const isFinal = status === "succeeded" || status === "failed" || status === "cancelled";

  return (
    <div className="flex min-h-screen items-center justify-center bg-background px-gutter">
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        className="w-full max-w-md rounded-2xl border border-outline-variant/20 bg-surface-container-low p-xl text-center"
      >
        <div className={`mx-auto mb-lg flex h-24 w-24 items-center justify-center rounded-full bg-primary/10 ${ui.tone}`}>
          <Icon
            name={ui.icon}
            filled
            className={`text-[52px] ${ui.spin ? "animate-spin" : ""}`}
          />
        </div>

        <h1 className={`font-headline-md text-headline-md font-bold ${ui.tone}`}>{ui.text}</h1>
        <p className="mt-xs font-body-md text-body-md text-secondary">{ui.sub}</p>

        {purchase && (
          <div className="mt-lg space-y-xs rounded-xl bg-surface-container p-md text-left font-body-md text-body-md">
            <div className="flex justify-between">
              <span className="text-secondary">Reference</span>
              <span className="font-bold text-on-surface">{purchase.reference}</span>
            </div>
            {purchase.amount && (
              <div className="flex justify-between">
                <span className="text-secondary">Amount</span>
                <span className="font-bold text-on-surface">{purchase.currency} {purchase.amount}</span>
              </div>
            )}
            {trackingId && (
              <div className="flex justify-between">
                <span className="text-secondary">PesaPal ID</span>
                <span className="font-bold text-on-surface truncate max-w-[180px]">{trackingId}</span>
              </div>
            )}
          </div>
        )}

        {purchase?.failure_reason && (
          <p className="mt-md font-body-md text-body-md text-error">{purchase.failure_reason}</p>
        )}

        {error && (
          <p className="mt-md font-body-md text-body-md text-error">{error}</p>
        )}

        {isFinal && (
          <button
            onClick={() => navigate(status === "succeeded" ? "/" : "/store")}
            className="mt-xl w-full rounded-full bg-primary py-md font-headline-md text-headline-md font-bold text-on-primary transition-all hover:brightness-110"
          >
            {status === "succeeded" ? "Go to Home" : "Back to Store"}
          </button>
        )}

        {!isFinal && !error && (
          <p className="mt-lg font-label-sm text-label-sm text-secondary animate-pulse">
            Do not close this page…
          </p>
        )}
      </motion.div>
    </div>
  );
}
