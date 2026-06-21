import { useEffect, useRef, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { Reveal, StaggerGroup, StaggerItem } from "@/components/Reveal";
import { Button } from "@/components/ui/button";
import { artistApi, type ArtistRelease as Release, type ArtistTrack as Track, type ArtistUser } from "@/lib/artistApi";

const FALLBACK_COVER = "https://lh3.googleusercontent.com/aida-public/AB6AXuChqVpeUCEFs5U7WuEaY64Tg7gQTzHxHYGBIR_YhsRc_ZhKVx5O5_yip_OweNC0jPzTpJ6X9fKbk0bf4srrI6ij6PcWFdkZy4zHo_SlNLZMXLkLBsD6RRe9eqQUDdIFl1PSbssKB0lq5ndbplCdRmGLruSZgczH6dhDdDdcdxqGLqW7XnsPzcSLbodcWzjANZyDAx1u5rYqkaplsLAPwNmxyrI5Z_tbCq4klIwJ5taoKzbT09KaWge-Mbybr6yt-0S3J9pe1VlxwKs";

type UploadTrack = Track & { file?: File; previewFile?: File; previewLocalUrl?: string };

export function ArtistDashboard() {
  const navigate = useNavigate();
  const [releases, setReleases] = useState<Release[]>([]);
  const [artist, setArtist] = useState<ArtistUser | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [studioOpen, setStudioOpen] = useState(false);
  const [activePreview, setActivePreview] = useState<string | null>(null);

  useEffect(() => {
    Promise.all([artistApi.me(), artistApi.releases()])
      .then(([session, catalog]) => {
        setArtist(session.user);
        setReleases(catalog.releases);
      })
      .catch(() => navigate("/artist/login", { replace: true }))
      .finally(() => setLoading(false));
  }, [navigate]);

  const saveRelease = async (form: FormData) => {
    setError("");
    try {
      const result = await artistApi.createRelease(form);
      setReleases((items) => [result.release, ...items]);
      setStudioOpen(false);
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "The release could not be saved.");
      throw reason;
    }
  };

  if (loading) return <main className="flex min-h-screen items-center justify-center"><Icon name="progress_activity" className="animate-spin text-primary text-[42px]" /></main>;

  const logout = async () => {
    await artistApi.logout();
    navigate("/artist/login", { replace: true });
  };

  return (
    <div className="min-h-screen bg-background text-on-background">
      <header className="fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between border-b border-outline-variant/15 bg-background/85 px-container-margin backdrop-blur-xl">
        <div className="flex items-center gap-sm">
          <span className="flex h-9 w-9 items-center justify-center rounded-full bg-primary-container text-on-primary-container"><Icon name="graphic_eq" filled /></span>
          <div className="leading-none"><span className="block font-headline-md text-headline-md font-black tracking-tighter text-primary">SoundRedeem</span><span className="font-label-sm text-label-sm uppercase tracking-widest text-secondary">Artist Studio</span></div>
        </div>
        <div className="flex items-center gap-sm">
          <span className="hidden font-label-md text-label-md text-secondary sm:block">{artist?.email}</span>
          <button onClick={() => void logout()} className="flex h-10 items-center gap-xs rounded-full bg-surface-container-high px-md font-label-md text-label-md text-secondary transition-colors hover:text-error"><Icon name="logout" className="text-[18px]" />Sign out</button>
        </div>
      </header>
      <main className="mx-auto max-w-7xl space-y-xl px-container-margin pb-16 pt-24">
      <Reveal direction="down">
        <section className="relative overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container p-xl">
          <div className="pointer-events-none absolute inset-0 bg-gradient-to-br from-primary/20 via-transparent to-transparent" />
          <div className="pointer-events-none absolute -right-20 -top-24 h-80 w-80 rounded-full bg-primary/15 blur-[100px]" />
          <div className="relative flex flex-col gap-lg md:flex-row md:items-end md:justify-between">
            <div className="flex items-center gap-lg">
              <div className="flex h-20 w-20 shrink-0 items-center justify-center rounded-full border-4 border-background bg-gradient-to-br from-primary-container to-primary text-on-primary spotify-shadow"><Icon name="graphic_eq" className="text-[42px]" filled /></div>
              <div><span className="font-label-md text-label-md uppercase tracking-widest text-primary">Artist workspace</span><h1 className="mt-xs font-display-lg text-display-lg leading-none">{artist?.artist_name}</h1><p className="mt-sm font-body-md text-body-md text-secondary">Verified artist · Manage releases and previews</p></div>
            </div>
            <div className="flex flex-wrap gap-sm"><Button variant="outline"><Icon name="visibility" />View store</Button><Button onClick={() => setStudioOpen(true)}><Icon name="upload" />Upload music</Button></div>
          </div>
        </section>
      </Reveal>

      <StaggerGroup className="grid grid-cols-2 gap-gutter xl:grid-cols-4" stagger={0.08}>
        {[
          ["Total Releases", releases.length.toLocaleString(), "confirmation_number"],
          ["Total Tracks", releases.reduce((sum, r) => sum + r.tracks.length, 0).toLocaleString(), "audio_file"],
          ["Total Redemptions", releases.reduce((sum, r) => sum + r.redemptions, 0).toLocaleString(), "download"],
          ["Sample Plays", releases.reduce((sum, r) => sum + r.tracks.reduce((s, t) => s + (t.samplePlays ?? 0), 0), 0).toLocaleString(), "play_circle"],
        ].map(([label, value, icon]) => (
          <StaggerItem key={label}>
            <div className="bento-card h-full rounded-xl border border-outline-variant/10 p-md">
              <div className="mb-md flex items-center justify-between">
                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-container-high text-primary"><Icon name={icon} /></div>
              </div>
              <p className="font-headline-lg text-headline-lg font-black">{value}</p>
              <p className="mt-xs font-label-md text-label-md uppercase tracking-widest text-secondary">{label}</p>
            </div>
          </StaggerItem>
        ))}
      </StaggerGroup>

      {error && <div role="alert" className="flex items-center gap-sm rounded-xl border border-error/30 bg-error-container/20 p-md text-error"><Icon name="error" /><span>{error}</span></div>}

      <section>
        <Reveal><div className="mb-md flex items-end justify-between px-xs"><div><h2 className="font-headline-md text-headline-md">Music catalog</h2><p className="font-body-md text-body-md text-secondary">Upload singles or albums and configure samples before purchase.</p></div><button onClick={() => setStudioOpen(true)} className="font-label-md text-label-md text-primary hover:underline">ADD RELEASE</button></div></Reveal>
        <StaggerGroup className="grid grid-cols-1 gap-gutter md:grid-cols-2 xl:grid-cols-3" stagger={0.08}>
          {releases.map((release) => <StaggerItem key={release.id}><ReleaseCard release={release} activePreview={activePreview} setActivePreview={setActivePreview} /></StaggerItem>)}
        </StaggerGroup>
      </section>

      <Reveal><section className="rounded-2xl border border-primary/20 bg-primary/5 p-xl"><div className="flex flex-col gap-lg md:flex-row md:items-center md:justify-between"><div className="flex items-start gap-md"><div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary"><Icon name="headphones" /></div><div><h2 className="font-headline-md text-headline-md">Preview before purchase</h2><p className="mt-xs max-w-2xl font-body-md text-body-md text-secondary">Every uploaded track can expose a 30-second sample. Customers hear the preview in the store before purchasing or redeeming the full release.</p></div></div><Button onClick={() => setStudioOpen(true)}><Icon name="add_circle" />Create release</Button></div></section></Reveal>

      {studioOpen && <UploadStudio onClose={() => setStudioOpen(false)} onSave={saveRelease} />}
      </main>
    </div>
  );
}


function ReleaseCard({ release, activePreview, setActivePreview }: { release: Release; activePreview: string | null; setActivePreview: (id: string | null) => void }) {
  const progress = Math.round((release.redemptions / release.limit) * 100);
  const playable = release.tracks.find((track) => track.previewUrl ?? track.audioUrl);
  return <article className="group overflow-hidden rounded-xl border border-outline-variant/10 bg-surface-container-low transition-colors hover:bg-surface-container-high"><div className="relative aspect-[16/10] overflow-hidden"><img src={release.image} alt={release.title} className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"/><div className="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"/><span className={`absolute left-md top-md rounded-full px-2 py-1 font-label-sm text-label-sm font-bold uppercase tracking-widest ${release.status === "Live" ? "bg-primary-container text-on-primary-container" : "bg-surface-container-highest text-secondary"}`}>{release.status}</span><div className="absolute bottom-md left-md right-md flex items-end justify-between"><div><p className="font-label-sm text-label-sm uppercase tracking-widest text-secondary">{release.type} · ${release.price}</p><h3 className="font-headline-md text-headline-md font-black">{release.title}</h3></div>{playable && <SamplePlayer releaseId={release.id} track={playable} playing={activePreview === playable.id} onPlaying={(playing) => setActivePreview(playing ? playable.id : null)} />}</div></div><div className="space-y-sm p-md"><div className="flex justify-between font-label-md text-label-md"><span className="text-secondary">{release.tracks.length} TRACK{release.tracks.length === 1 ? "" : "S"}</span><span>{release.redemptions.toLocaleString()} / {release.limit.toLocaleString()}</span></div><div className="h-1.5 overflow-hidden rounded-full bg-surface-container-highest"><div className="h-full rounded-full bg-primary" style={{ width: `${progress}%` }}/></div><div className="space-y-xs pt-xs">{release.tracks.slice(0, 3).map((track, index) => <div key={track.id} className="flex items-center gap-sm rounded-lg bg-background/30 px-sm py-xs"><span className="w-4 text-label-sm text-secondary">{index + 1}</span><span className="min-w-0 flex-1 truncate text-body-md">{track.title}</span>{track.audioUrl ? <Icon name="graphic_eq" className="text-primary text-[18px]"/> : <span className="text-label-sm text-secondary">No sample</span>}</div>)}</div></div></article>;
}

function SamplePlayer({ releaseId, track, playing, onPlaying }: { releaseId: string; track: Track; playing: boolean; onPlaying: (playing: boolean) => void }) {
  const audio = useRef<HTMLAudioElement>(null);
  const [progress, setProgress] = useState(0);
  const src = track.previewUrl ?? track.audioUrl;
  const limitTo30 = !track.previewUrl;
  useEffect(() => { if (!playing) audio.current?.pause(); else void audio.current?.play(); }, [playing]);
  return <div className="flex items-center gap-xs"><audio ref={audio} src={src} onTimeUpdate={(e) => { const node = e.currentTarget; if (limitTo30 && node.currentTime >= 30) { node.pause(); node.currentTime = 0; setProgress(0); onPlaying(false); } else setProgress((node.currentTime / (limitTo30 ? Math.min(node.duration || 30, 30) : (node.duration || 1))) * 100); }} onEnded={() => { setProgress(0); onPlaying(false); }}/><button type="button" onClick={() => { const next = !playing; onPlaying(next); if (next) void artistApi.samplePlayed(releaseId, track.id); }} aria-label={`${playing ? "Pause" : "Play"} sample`} className="relative flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-primary-container text-on-primary-container shadow-xl"><span className="absolute bottom-0 left-0 h-1 bg-primary-fixed" style={{ width: `${progress}%` }}/><Icon name={playing ? "pause" : "play_arrow"} filled /></button><span className="hidden font-label-sm text-label-sm text-white sm:block">{limitTo30 ? "30s" : "Preview"}</span></div>;
}

function UploadStudio({ onClose, onSave }: { onClose: () => void; onSave: (form: FormData) => Promise<void> }) {
  const [type, setType] = useState<"Single" | "Album">("Single");
  const [title, setTitle] = useState("");
  const [price, setPrice] = useState("2.99");
  const [cover, setCover] = useState(FALLBACK_COVER);
  const [coverFile, setCoverFile] = useState<File | null>(null);
  const [tracks, setTracks] = useState<UploadTrack[]>([{ id: crypto.randomUUID(), title: "", fileName: "" }]);
  const [error, setError] = useState("");
  const [saving, setSaving] = useState(false);

  const changeType = (next: "Single" | "Album") => { setType(next); setPrice(next === "Single" ? "2.99" : "9.99"); if (next === "Single") setTracks((items) => [items[0]]); };
  const addTrack = () => setTracks((items) => [...items, { id: crypto.randomUUID(), title: "", fileName: "" }]);
  const updateTrack = (id: string, patch: Partial<UploadTrack>) => setTracks((items) => items.map((track) => track.id === id ? { ...track, ...patch } : track));
  const submit = async (event: { preventDefault(): void }, status: "Live" | "Draft") => {
    event.preventDefault();
    setError("");
    if (!coverFile || !title.trim() || tracks.some((track) => !track.title.trim() || !track.file)) {
      setError("Add cover artwork, a title, and an audio file for every track.");
      return;
    }
    const form = new FormData();
    form.append("title", title);
    form.append("release_type", type.toLowerCase());
    form.append("price", price);
    form.append("status", status === "Live" ? "live" : "scheduled");
    form.append("cover", coverFile);
    tracks.forEach((track, index) => {
      form.append("track_titles[]", track.title);
      form.append("tracks[]", track.file as File);
      if (track.previewFile) {
        form.append(`previews[${index}]`, track.previewFile);
      }
    });
    setSaving(true);
    try {
      await onSave(form);
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Upload failed.");
    } finally {
      setSaving(false);
    }
  };

  return <div className="fixed inset-0 z-[100] flex items-end justify-center bg-black/80 p-0 backdrop-blur-sm md:items-center md:p-lg" role="dialog" aria-modal="true" aria-label="Upload music"><div className="max-h-[94dvh] w-full max-w-4xl overflow-y-auto rounded-t-2xl border border-outline-variant/20 bg-background md:rounded-2xl"><div className="sticky top-0 z-20 flex items-center justify-between border-b border-outline-variant/15 bg-background/90 p-lg backdrop-blur-xl"><div><span className="font-label-md text-label-md uppercase tracking-widest text-primary">Artist upload studio</span><h2 className="font-headline-lg text-headline-lg">Create a release</h2></div><button onClick={onClose} className="flex h-10 w-10 items-center justify-center rounded-full bg-surface-container-high text-secondary hover:text-white"><Icon name="close"/></button></div><form className="grid grid-cols-1 gap-lg p-lg md:grid-cols-3" onSubmit={(event) => void submit(event, "Live")}><div className="space-y-md"><label className="group relative block aspect-square cursor-pointer overflow-hidden rounded-xl border border-dashed border-outline bg-surface-container-low"><img src={cover} alt="Cover preview" className="h-full w-full object-cover opacity-70"/><div className="absolute inset-0 flex flex-col items-center justify-center bg-black/30"><Icon name="add_photo_alternate" className="text-[42px] text-primary"/><span className="mt-xs font-label-md text-label-md">UPLOAD COVER</span></div><input required type="file" accept="image/jpeg,image/png,image/webp" className="hidden" onChange={(e) => { const file = e.target.files?.[0]; if (file) { setCoverFile(file); setCover(URL.createObjectURL(file)); } }}/></label><p className="text-center font-label-sm text-label-sm text-secondary">Square JPG, PNG or WEBP · max 5 MB</p></div><div className="space-y-lg md:col-span-2"><div className="grid grid-cols-2 gap-xs rounded-xl bg-surface-container-low p-1">{(["Single","Album"] as const).map((item) => <button key={item} type="button" onClick={() => changeType(item)} className={`rounded-lg py-sm font-label-md text-label-md ${type === item ? "bg-surface-container-highest text-primary" : "text-secondary"}`}>{item.toUpperCase()}</button>)}</div><div className="grid grid-cols-1 gap-md sm:grid-cols-[1fr_140px]"><Field label="Release title"><input required value={title} onChange={(e) => setTitle(e.target.value)} placeholder={type === "Single" ? "Single title" : "Album title"} className="artist-input"/></Field><Field label="Price (USD)"><input required type="number" min="0" step="0.01" value={price} onChange={(e) => setPrice(e.target.value)} className="artist-input"/></Field></div><div><div className="mb-sm flex items-center justify-between"><div><h3 className="font-body-lg text-body-lg font-bold">Tracks & samples</h3><p className="font-label-sm text-label-sm text-secondary">Upload the full song and an optional preview clip for the store.</p></div>{type === "Album" && <Button type="button" size="sm" variant="outline" onClick={addTrack}><Icon name="add"/>Track</Button>}</div><div className="space-y-sm">{tracks.map((track, index) => <div key={track.id} className="rounded-xl border border-outline-variant/15 bg-surface-container-low p-md space-y-sm"><div className="flex items-center gap-sm"><span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-surface-container-high text-primary">{index + 1}</span><input required value={track.title} onChange={(e) => updateTrack(track.id, { title: e.target.value })} placeholder="Track title" className="artist-input min-w-0 flex-1"/>{type === "Album" && tracks.length > 1 && <button type="button" onClick={() => setTracks((items) => items.filter((item) => item.id !== track.id))} className="text-secondary hover:text-error"><Icon name="delete"/></button>}</div><label className="flex cursor-pointer items-center justify-between rounded-lg bg-surface-container-high px-md py-sm"><div className="flex min-w-0 items-center gap-sm"><Icon name={track.file ? "check_circle" : "audio_file"} className={track.file ? "text-primary" : "text-secondary"}/><div className="min-w-0"><p className="font-label-xs text-label-sm uppercase tracking-widest text-secondary">Full song</p><p className="truncate text-body-md">{track.fileName || "WAV, MP3, FLAC or M4A"}</p></div></div><span className="font-label-sm text-label-sm text-primary shrink-0">BROWSE</span><input required type="file" accept="audio/*,.wav,.mp3,.flac,.m4a" className="hidden" onChange={(e) => { const file = e.target.files?.[0]; if (file) updateTrack(track.id, { file, fileName: file.name, audioUrl: URL.createObjectURL(file) }); }}/></label>{track.audioUrl && <audio controls src={track.audioUrl} className="h-9 w-full"/>}<label className="flex cursor-pointer items-center justify-between rounded-lg border border-dashed border-outline-variant/40 bg-surface-container px-md py-sm"><div className="flex min-w-0 items-center gap-sm"><Icon name={track.previewFile ? "check_circle" : "play_circle"} className={track.previewFile ? "text-primary" : "text-secondary"}/><div className="min-w-0"><p className="font-label-xs text-label-sm uppercase tracking-widest text-secondary">Preview clip <span className="normal-case tracking-normal">(optional)</span></p><p className="truncate text-body-md">{track.previewFile ? track.previewFile.name : "Custom clip played in the store"}</p></div></div><span className="font-label-sm text-label-sm text-primary shrink-0">BROWSE</span><input type="file" accept="audio/*,.wav,.mp3,.flac,.m4a" className="hidden" onChange={(e) => { const file = e.target.files?.[0]; if (file) updateTrack(track.id, { previewFile: file, previewLocalUrl: URL.createObjectURL(file) }); }}/></label>{track.previewLocalUrl && <audio controls src={track.previewLocalUrl} className="h-9 w-full"/>}</div>)}</div></div>{error && <div role="alert" className="rounded-xl border border-error/30 bg-error-container/20 p-md text-body-md text-error">{error}</div>}<div className="flex flex-col-reverse gap-sm border-t border-outline-variant/15 pt-lg sm:flex-row sm:justify-end"><Button type="button" variant="ghost" onClick={onClose}>Cancel</Button><Button type="button" variant="outline" disabled={saving} onClick={(event) => void submit(event, "Draft")}><Icon name="save"/>Save draft</Button><Button type="submit" disabled={saving}>{saving ? <><Icon name="progress_activity" className="animate-spin"/>Uploading…</> : <><Icon name="publish"/>Publish release</>}</Button></div></div></form></div></div>;
}

function Field({ label, children }: { label: string; children: React.ReactNode }) { return <label className="space-y-xs"><span className="font-label-md text-label-md uppercase tracking-widest text-secondary">{label}</span>{children}</label>; }
