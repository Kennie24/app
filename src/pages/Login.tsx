import { useState, type FormEvent } from "react";
import { Link, useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import { Icon } from "@/components/Icon";
import { CountryPhoneInput } from "@/components/CountryPhoneInput";
import { artistApi } from "@/lib/artistApi";
import { fanApi } from "@/lib/fanApi";

type Method = "phone" | "email";

export function Login() {
  const navigate = useNavigate();
  const [method, setMethod] = useState<Method>("email");
  const [value, setValue] = useState("");
  const [password, setPassword] = useState("");
  const [remember, setRemember] = useState(true);
  const [checking, setChecking] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");

  const submit = async (event: FormEvent) => {
    event.preventDefault();
    const trimmed = value.trim();
    if (!trimmed) return;
    setError("");
    setMessage("");

    if (method === "email") {
      if (!/.+@.+\..+/.test(trimmed)) {
        setError("Enter a valid email address.");
        return;
      }
      if (!password) {
        setError("Enter your password.");
        return;
      }

      setChecking(true);
      try {
        // Route artists to the Laravel-hosted artist studio login (cookie scoped there).
        try {
          await artistApi.checkEmail(trimmed);
          const studio = (import.meta.env.VITE_ARTIST_STUDIO_URL
            ?? `${window.location.protocol}//${window.location.hostname}:8000/artist-studio`) as string;
          window.location.href = `${studio}/login?email=${encodeURIComponent(trimmed)}`;
          return;
        } catch {
          // Not an artist — try fan login.
        }

        await fanApi.login(trimmed, password, remember);
        navigate("/");
      } catch (reason) {
        setError(reason instanceof Error ? reason.message : "Could not sign in.");
      } finally {
        setChecking(false);
      }
      return;
    }

    // Phone-only login isn't implemented yet — phone accounts must use the verified-redeem flow.
    setMessage("Phone sign-in is not connected yet. Use email + password, or create an account.");
  };

  return (
    <div className="relative min-h-screen overflow-hidden bg-[#121212] text-on-background">
      <header className="fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between bg-[#121212]/80 px-gutter backdrop-blur-xl">
        <Link to="/" className="flex items-center gap-base">
          <Icon name="music_note" className="text-primary text-headline-md" filled />
          <span className="font-headline-md text-headline-md font-black tracking-tight text-primary">Titan Takuba Music</span>
        </Link>
        <Link to="/signup" className="font-label-md text-label-md text-secondary hover:text-primary transition-colors">SIGN UP</Link>
      </header>

      <motion.main
        initial={{ opacity: 0, y: 18 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.55, ease: [0.22, 1, 0.36, 1] }}
        className="relative z-10 mx-auto flex min-h-screen w-full max-w-md flex-col px-container-margin pb-xl pt-24"
      >
        <div className="mb-xl text-center">
          <h1 className="font-headline-lg-mobile text-headline-lg-mobile text-on-background">Welcome back</h1>
          <p className="mt-xs font-body-md text-body-md text-secondary">Sign in to your account to continue.</p>
        </div>

        <div className="mb-lg flex border-b border-surface-container-highest" role="tablist">
          {(["email", "phone"] as Method[]).map((tab) => (
            <button
              key={tab}
              type="button"
              role="tab"
              aria-selected={method === tab}
              onClick={() => { setMethod(tab); setValue(""); setPassword(""); setError(""); setMessage(""); }}
              className={`flex-1 border-b-2 py-md font-label-md text-label-md transition-all ${
                method === tab ? "border-primary text-white" : "border-transparent text-secondary"
              }`}
            >
              {tab === "email" ? "Email" : "Phone number"}
            </button>
          ))}
        </div>

        <form onSubmit={(e) => void submit(e)} className="mb-xl space-y-lg">
          <motion.div key={method} initial={{ opacity: 0, x: 12 }} animate={{ opacity: 1, x: 0 }} className="space-y-lg">
            <div>
              <label className="mb-xs ml-base block font-label-md text-label-md text-on-surface-variant">
                {method === "phone" ? "Phone number" : "Email address"}
              </label>
              {method === "phone" ? (
                <CountryPhoneInput value={value} onChange={setValue} required />
              ) : (
                <input
                  required
                  value={value}
                  onChange={(event) => { setValue(event.target.value); setError(""); }}
                  className="w-full rounded-lg border border-transparent bg-surface-container-high px-md py-md font-body-lg text-body-lg outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-1 focus:ring-primary"
                  placeholder="email@example.com"
                  type="email"
                  autoComplete="email"
                />
              )}
            </div>

            {method === "email" && (
              <div>
                <label className="mb-xs ml-base block font-label-md text-label-md text-on-surface-variant">Password</label>
                <input
                  required
                  value={password}
                  onChange={(event) => { setPassword(event.target.value); setError(""); }}
                  className="w-full rounded-lg border border-transparent bg-surface-container-high px-md py-md font-body-lg text-body-lg outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-1 focus:ring-primary"
                  placeholder="Your password"
                  type="password"
                  autoComplete="current-password"
                />
                <label className="mt-sm ml-base flex cursor-pointer items-center gap-sm font-label-md text-label-md text-secondary">
                  <input
                    type="checkbox"
                    checked={remember}
                    onChange={(e) => setRemember(e.target.checked)}
                    className="h-4 w-4 accent-primary"
                  />
                  Keep me signed in
                </label>
              </div>
            )}
          </motion.div>

          <button disabled={checking} className="w-full rounded-full bg-primary-container py-md font-body-lg text-body-lg font-bold text-[#191414] shadow-lg transition-all hover:bg-[#1ed760] hover:scale-[1.02] active:scale-95 disabled:opacity-60">
            {checking ? "Signing in…" : "Continue"}
          </button>

          {error && (
            <div role="alert" className="rounded-xl border border-error/40 bg-error-container/20 p-md text-center text-body-md text-error">
              {error}
            </div>
          )}
          {message && (
            <div role="status" className="rounded-xl border border-outline-variant/30 bg-surface-container-low p-md text-center text-body-md text-secondary">
              {message}
            </div>
          )}
        </form>

        <div className="mt-auto pt-xl text-center">
          <p className="px-md font-body-md text-body-md text-on-surface-variant">
            By continuing, you agree to Titan Takuba Music&apos;s <a className="font-bold text-on-surface hover:underline" href="#">Terms of Service</a> and <a className="font-bold text-on-surface hover:underline" href="#">Privacy Policy</a>.
          </p>
          <p className="mt-lg font-body-md text-body-md text-secondary">
            New here? <Link to="/signup" className="font-bold text-primary hover:underline">Create an account</Link>
          </p>
        </div>
      </motion.main>

      <div className="pointer-events-none fixed inset-0 overflow-hidden">
        <div className="absolute -right-[10%] -top-[10%] h-[50%] w-[50%] rounded-full bg-primary/5 blur-[120px]" />
        <div className="absolute -bottom-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-primary/5 blur-[100px]" />
      </div>
    </div>
  );
}
