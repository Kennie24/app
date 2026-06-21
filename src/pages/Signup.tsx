import { useState, type FormEvent } from "react";
import { Link, useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import { Icon } from "@/components/Icon";
import { CountryPhoneInput } from "@/components/CountryPhoneInput";
import { fanApi } from "@/lib/fanApi";

type Method = "email" | "phone";

export function Signup() {
  const navigate = useNavigate();
  const [method, setMethod] = useState<Method>("email");
  const [showPassword, setShowPassword] = useState(false);
  const [phone, setPhone] = useState("");
  const [message, setMessage] = useState("");
  const [submitting, setSubmitting] = useState(false);

  const submit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setMessage("");
    setSubmitting(true);

    try {
      const form = new FormData(event.currentTarget);

      if (method === "email") {
        await fanApi.registerEmail(
          String(form.get("name") ?? ""),
          String(form.get("email") ?? ""),
          String(form.get("password") ?? ""),
        );
      } else {
        await fanApi.registerPhone(String(form.get("phone_e164") ?? ""));
      }

      navigate("/scan", { replace: true });
    } catch (reason) {
      setMessage(reason instanceof Error ? reason.message : "Account creation failed.");
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="relative min-h-screen overflow-hidden bg-background text-on-background">
      <header className="fixed inset-x-0 top-0 z-50 flex h-16 items-center bg-surface/80 px-container-margin backdrop-blur-xl">
        <Link to="/login" aria-label="Back to login" className="-ml-2 p-2 text-on-surface-variant transition-colors hover:text-primary active:scale-95">
          <Icon name="arrow_back" />
        </Link>
        <Link to="/login" className="ml-4 font-headline-lg-mobile text-headline-lg-mobile font-black tracking-tighter text-on-surface uppercase">
          Redeem Music
        </Link>
      </header>

      <motion.main
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
        className="relative z-10 mx-auto w-full max-w-md px-container-margin pb-12 pt-24"
      >
        <div className="mb-10 space-y-2">
          <h1 className="font-display-lg text-display-lg leading-tight tracking-tight">Create your account</h1>
          <p className="font-body-lg text-body-lg text-on-surface-variant/80">Join the world of high-fidelity audio redemption.</p>
        </div>

        <div className="relative mb-8 flex w-full items-center rounded-xl border border-white/5 bg-surface-container-low p-1 shadow-inner">
          <motion.div
            className="absolute left-1 top-1 h-10 w-[calc(50%-4px)] rounded-lg bg-surface-variant shadow-md"
            animate={{ x: method === "phone" ? 0 : "100%" }}
            transition={{ type: "spring", stiffness: 360, damping: 32 }}
            style={{ width: "calc(50% - 4px)" }}
          />
          <button type="button" onClick={() => { setMethod("phone"); setMessage(""); }} className={`relative z-10 w-1/2 py-2 text-center font-label-md text-label-md transition-colors ${method === "phone" ? "text-on-surface" : "text-on-surface-variant"}`}>Phone</button>
          <button type="button" onClick={() => { setMethod("email"); setMessage(""); }} className={`relative z-10 w-1/2 py-2 text-center font-label-md text-label-md transition-colors ${method === "email" ? "text-on-surface" : "text-on-surface-variant"}`}>Email</button>
        </div>

        <motion.form key={method} onSubmit={submit} initial={{ opacity: 0, x: 12 }} animate={{ opacity: 1, x: 0 }} className="space-y-6">
          {method === "email" ? (
            <>
              <AuthField label="Full Name" name="name" placeholder="Enter your full name" autoComplete="name" />
              <AuthField label="Email Address" name="email" placeholder="email@example.com" type="email" autoComplete="email" />
              <div className="space-y-2">
                <label htmlFor="password" className="px-1 font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Create Password</label>
                <div className="relative">
                  <input
                    id="password"
                    name="password"
                    required
                    minLength={8}
                    type={showPassword ? "text" : "password"}
                    autoComplete="new-password"
                    placeholder="Min. 8 characters"
                    className="h-14 w-full rounded-xl border border-transparent bg-surface-container-high px-4 pr-12 text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-on-surface-variant"
                  />
                  <button type="button" onClick={() => setShowPassword((visible) => !visible)} aria-label={showPassword ? "Hide password" : "Show password"} className="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 hover:text-primary">
                    <Icon name={showPassword ? "visibility_off" : "visibility"} />
                  </button>
                </div>
              </div>
            </>
          ) : (
            <>
              <div className="space-y-2">
                <label htmlFor="phone" className="px-1 font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Phone Number</label>
                <CountryPhoneInput id="signup-phone" value={phone} onChange={setPhone} required />
              </div>
              <p className="px-4 text-center text-xs text-on-surface-variant/60">Your phone number will be saved as your fan account identity.</p>
            </>
          )}

          <div className="pt-4">
            <button disabled={submitting} className="h-14 w-full rounded-full bg-primary-container font-headline-md text-headline-md text-on-primary-container shadow-lg shadow-primary-container/20 transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-60">
              {submitting ? "Creating account…" : "Create Account"}
            </button>
          </div>
          {message && (
            <div role="status" className="rounded-xl border border-outline-variant/30 bg-surface-container-low p-md text-center text-body-md text-secondary">
              {message}
            </div>
          )}
        </motion.form>

        <p className="mb-12 text-center font-label-sm text-label-sm leading-relaxed text-on-surface-variant/40">
          By signing up, you agree to SoundRedeem&apos;s <a className="text-on-surface underline" href="#">Terms of Service</a> and <a className="text-on-surface underline" href="#">Privacy Policy</a>.
        </p>

        <div className="border-t border-white/5 pt-6 text-center">
          <p className="font-body-md text-body-md text-on-surface-variant">Already have an account? <Link className="ml-1 font-bold text-primary hover:underline" to="/login">Log in</Link></p>
        </div>
      </motion.main>

      <div className="pointer-events-none fixed inset-0">
        <div className="absolute right-0 top-0 h-96 w-96 translate-x-1/2 -translate-y-1/2 rounded-full bg-primary-container/10 blur-[120px]" />
        <div className="absolute bottom-0 left-0 h-80 w-80 -translate-x-1/2 translate-y-1/2 rounded-full bg-tertiary-container/5 blur-[100px]" />
      </div>
    </div>
  );
}

function AuthField({ label, name, placeholder, type = "text", autoComplete }: { label: string; name: string; placeholder: string; type?: string; autoComplete?: string }) {
  return (
    <div className="space-y-2">
      <label htmlFor={name} className="px-1 font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">{label}</label>
      <input id={name} name={name} required type={type} autoComplete={autoComplete} placeholder={placeholder} className="h-14 w-full rounded-xl border border-transparent bg-surface-container-high px-4 text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-on-surface-variant" />
    </div>
  );
}
