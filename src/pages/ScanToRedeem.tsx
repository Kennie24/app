import { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Scanner, type IDetectedBarcode } from "@yudiel/react-qr-scanner";
import { Icon } from "@/components/Icon";
import { Reveal } from "@/components/Reveal";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

type ScanState =
  | { kind: "idle" }
  | { kind: "scanning" }
  | { kind: "denied"; message: string }
  | { kind: "success"; code: string }
  | { kind: "error"; message: string };

type Facing = "environment" | "user";

export function ScanToRedeem() {
  const navigate = useNavigate();
  const [state, setState] = useState<ScanState>({ kind: "idle" });
  const [facing, setFacing] = useState<Facing>("environment");
  const [paused, setPaused] = useState(false);
  const [manualCode, setManualCode] = useState("");

  // Detect basic camera support up front so we can show a useful fallback.
  const hasCameraSupport = useMemo(() => {
    return (
      typeof navigator !== "undefined" &&
      !!navigator.mediaDevices &&
      typeof navigator.mediaDevices.getUserMedia === "function"
    );
  }, []);

  // Stop scanning when leaving the page or hiding the tab.
  useEffect(() => {
    const onVisibility = () => {
      if (document.hidden && state.kind === "scanning") setPaused(true);
    };
    document.addEventListener("visibilitychange", onVisibility);
    return () => document.removeEventListener("visibilitychange", onVisibility);
  }, [state.kind]);

  const startScan = () => {
    if (!hasCameraSupport) {
      setState({
        kind: "error",
        message: "This device or browser does not expose a camera to web pages.",
      });
      return;
    }
    setPaused(false);
    setState({ kind: "scanning" });
  };

  const resetScan = () => {
    setState({ kind: "idle" });
    setPaused(false);
  };

  const onDetected = (codes: IDetectedBarcode[]) => {
    if (!codes.length) return;
    const value = codes[0].rawValue.trim();
    if (!value) return;
    setPaused(true);
    setState({ kind: "success", code: value });
  };

  const onError = (err: unknown) => {
    const message =
      err instanceof Error ? err.message : "Could not access the camera.";
    const denied = /permission|denied|notallowed/i.test(message);
    setState(
      denied
        ? { kind: "denied", message }
        : { kind: "error", message }
    );
  };

  const submitManual = (e: React.FormEvent) => {
    e.preventDefault();
    const code = manualCode.trim();
    if (!code) return;
    setState({ kind: "success", code });
  };

  const isScanning = state.kind === "scanning" && !paused;
  const isSuccess = state.kind === "success";

  return (
    <main className="min-h-screen pt-32 pb-32 px-container-margin flex flex-col items-center justify-center">
      {/* Status pill */}
      <Reveal direction="down" duration={0.5}>
        <div className="mb-lg flex items-center gap-sm bg-surface-container-high px-md py-sm rounded-full border border-outline-variant/30">
          <div className="relative flex items-center justify-center">
            <span
              className={
                isScanning
                  ? "absolute inline-flex h-3 w-3 rounded-full bg-primary animate-ping opacity-75"
                  : "hidden"
              }
            />
            <span
              className={
                "relative inline-flex rounded-full h-3 w-3 " +
                (isSuccess
                  ? "bg-primary"
                  : isScanning
                  ? "bg-primary"
                  : state.kind === "denied" || state.kind === "error"
                  ? "bg-error"
                  : "bg-secondary")
              }
            />
          </div>
          <span
            className={
              "font-label-md text-label-md tracking-widest uppercase " +
              (isSuccess
                ? "text-primary"
                : isScanning
                ? "text-primary"
                : state.kind === "denied" || state.kind === "error"
                ? "text-error"
                : "text-secondary")
            }
          >
            {isSuccess
              ? "Code Detected"
              : isScanning
              ? "Scanning Active"
              : state.kind === "denied"
              ? "Camera Blocked"
              : state.kind === "error"
              ? "Camera Error"
              : "Ready to Scan"}
          </span>
        </div>
      </Reveal>

      {/* Scanner viewport */}
      <Reveal direction="scale" duration={0.8} delay={0.1}>
        <div className="relative group">
          <div className="absolute -inset-8 bg-primary/10 rounded-full blur-3xl animate-pulse-slow" />
          <div className="relative z-10 p-gutter bg-surface-container-low rounded-xl border border-outline-variant shadow-2xl">
            <div className="relative w-64 h-64 md:w-80 md:h-80 overflow-hidden rounded-lg bg-surface-container-lowest">
              {/* Live camera */}
              {state.kind === "scanning" && (
                <>
                  <Scanner
                    onScan={onDetected}
                    onError={onError}
                    paused={paused}
                    constraints={{ facingMode: facing }}
                    formats={["qr_code", "code_128", "ean_13", "data_matrix"]}
                    components={{
                      finder: false,
                      torch: true,
                      zoom: false,
                      onOff: false,
                    }}
                    sound={false}
                    styles={{
                      container: {
                        width: "100%",
                        height: "100%",
                        position: "absolute",
                        inset: 0,
                      },
                      video: {
                        width: "100%",
                        height: "100%",
                        objectFit: "cover",
                      },
                    }}
                  />
                  <div className="scanning-line z-20" />
                </>
              )}

              {/* Success overlay */}
              {isSuccess && (
                <div className="absolute inset-0 flex flex-col items-center justify-center gap-sm bg-surface-container-lowest/95 z-30">
                  <div className="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center">
                    <Icon name="check_circle" className="text-primary text-[40px]" filled />
                  </div>
                  <p className="font-label-md text-label-md text-primary uppercase tracking-widest">
                    Captured
                  </p>
                  <p className="font-body-md text-body-md text-on-surface px-md text-center break-all max-w-[260px]">
                    {state.code}
                  </p>
                </div>
              )}

              {/* Error / denied overlay */}
              {(state.kind === "denied" || state.kind === "error") && (
                <div className="absolute inset-0 flex flex-col items-center justify-center gap-xs bg-surface-container-lowest/95 z-30 p-md text-center">
                  <Icon
                    name={state.kind === "denied" ? "no_photography" : "error"}
                    className="text-error text-[40px]"
                    filled
                  />
                  <p className="font-label-md text-label-md text-error uppercase tracking-widest">
                    {state.kind === "denied" ? "Permission Denied" : "Camera Error"}
                  </p>
                  <p className="font-label-sm text-label-sm text-secondary max-w-[260px]">
                    {state.kind === "denied"
                      ? "Allow camera access in your browser settings, then try again."
                      : state.message}
                  </p>
                </div>
              )}

              {/* Idle placeholder */}
              {state.kind === "idle" && (
                <div className="absolute inset-0 flex flex-col items-center justify-center gap-sm bg-surface-container-lowest z-10 text-center px-md">
                  <Icon
                    name="qr_code_scanner"
                    className="text-primary text-[64px]"
                    filled
                  />
                  <p className="font-label-md text-label-md text-on-surface uppercase tracking-widest">
                    Tap to start camera
                  </p>
                  <p className="font-label-sm text-label-sm text-secondary max-w-[220px]">
                    Point your device at a QR code from a card, vinyl insert, or printed pass.
                  </p>
                </div>
              )}

              {/* Corner brackets — only meaningful while live */}
              {state.kind === "scanning" && (
                <>
                  <div className="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-primary m-4 z-20" />
                  <div className="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-primary m-4 z-20" />
                  <div className="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-primary m-4 z-20" />
                  <div className="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-primary m-4 z-20" />
                </>
              )}
            </div>
          </div>
        </div>
      </Reveal>

      {/* Copy */}
      <Reveal delay={0.25}>
        <div className="mt-xl text-center max-w-sm space-y-md">
          <h1 className="font-headline-lg text-headline-lg font-bold text-on-surface">
            {isSuccess ? "Code Captured" : "Ready to Redeem?"}
          </h1>
          <p className="font-body-lg text-body-lg text-secondary">
            {isSuccess
              ? "We read the code from your scan. Continue to verification, or scan another."
              : "Hold a printed QR code, vinyl insert, or digital pass steady in front of the camera to unlock your reward."}
          </p>
        </div>
      </Reveal>

      {/* Primary actions */}
      <Reveal delay={0.35} direction="up" className="w-full max-w-md">
        <div className="mt-xl flex flex-col gap-sm w-full">
          {state.kind === "idle" && (
            <Button onClick={startScan} className="w-full" size="lg">
              <Icon name="photo_camera" />
              Start Camera
            </Button>
          )}

          {state.kind === "scanning" && (
            <div className="grid grid-cols-2 gap-md">
              <button
                onClick={() =>
                  setFacing((f) => (f === "environment" ? "user" : "environment"))
                }
                className="flex flex-col items-center justify-center p-md bg-surface-container rounded-lg border border-outline-variant/30 hover:bg-surface-container-high transition-colors active:scale-95"
              >
                <Icon name="cameraswitch" className="text-primary mb-xs" />
                <span className="font-label-md text-label-md">Flip Camera</span>
              </button>
              <button
                onClick={resetScan}
                className="flex flex-col items-center justify-center p-md bg-surface-container rounded-lg border border-outline-variant/30 hover:bg-surface-container-high transition-colors active:scale-95"
              >
                <Icon name="stop_circle" className="text-error mb-xs" />
                <span className="font-label-md text-label-md">Stop</span>
              </button>
            </div>
          )}

          {isSuccess && (
            <div className="grid grid-cols-2 gap-md">
              <Button variant="outline" onClick={startScan}>
                <Icon name="restart_alt" />
                Scan Again
              </Button>
              <Button onClick={() => navigate(`/redeem?code=${encodeURIComponent(state.code)}`)}>
                <Icon name="verified" />
                Verify
              </Button>
            </div>
          )}

          {(state.kind === "denied" || state.kind === "error") && (
            <Button onClick={startScan} className="w-full">
              <Icon name="refresh" />
              Try Again
            </Button>
          )}
        </div>
      </Reveal>

      {/* Manual entry fallback */}
      <Reveal delay={0.45} className="w-full max-w-md">
        <form onSubmit={submitManual} className="mt-lg w-full">
          <div className="flex items-center gap-xs text-secondary mb-xs px-xs">
            <Icon name="keyboard" className="text-[16px]" />
            <span className="font-label-md text-label-md uppercase tracking-widest">
              Or enter the code manually
            </span>
          </div>
          <div className="flex flex-col sm:flex-row gap-sm">
            <Input
              value={manualCode}
              onChange={(e) => setManualCode(e.target.value)}
              placeholder="XXXX-XXXX-XXXX"
              className="flex-1"
              autoComplete="off"
            />
            <Button type="submit" variant="outline" disabled={!manualCode.trim()}>
              Submit
            </Button>
          </div>
        </form>
      </Reveal>

      {/* Security footer */}
      <Reveal delay={0.55}>
        <div className="mt-lg flex items-center gap-xs text-secondary/60">
          <Icon name="security" className="text-[16px]" />
          <span className="text-label-sm font-label-sm">
            Secure end-to-end verification powered by SoundRedeem
          </span>
        </div>
      </Reveal>
    </main>
  );
}
