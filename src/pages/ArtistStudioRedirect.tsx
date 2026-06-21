import { useEffect } from "react";

// The artist workspace now lives in the Laravel app at /artist-studio.
// This component immediately bounces the browser there so any existing
// links to /artist still land in the right place.
const ARTIST_STUDIO_URL = (import.meta.env.VITE_ARTIST_STUDIO_URL
  ?? `${window.location.protocol}//${window.location.hostname}:8000/artist-studio`) as string;

export function ArtistStudioRedirect() {
  useEffect(() => {
    window.location.replace(ARTIST_STUDIO_URL);
  }, []);

  return (
    <main className="flex min-h-screen items-center justify-center bg-background text-on-background">
      <div className="text-center">
        <div className="mx-auto mb-md h-12 w-12 animate-spin rounded-full border-4 border-primary border-t-transparent" />
        <p className="font-label-md text-label-md uppercase tracking-widest text-secondary">
          Opening artist studio…
        </p>
        <a href={ARTIST_STUDIO_URL} className="mt-sm inline-block text-primary hover:underline">
          Click here if you are not redirected.
        </a>
      </div>
    </main>
  );
}
