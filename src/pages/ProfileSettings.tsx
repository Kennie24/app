import { useEffect, useState, type FormEvent } from "react";
import { Link, useNavigate } from "react-router-dom";
import { Icon } from "@/components/Icon";
import { Reveal } from "@/components/Reveal";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { fanApi, type FanUser } from "@/lib/fanApi";

export function ProfileSettings() {
  const navigate = useNavigate();
  const [user, setUser] = useState<FanUser | null>(null);
  const [name, setName] = useState("");
  const [avatar, setAvatar] = useState<File | null>(null);
  const [preview, setPreview] = useState<string | null>(null);
  const [removeAvatar, setRemoveAvatar] = useState(false);
  const [profileMessage, setProfileMessage] = useState("");
  const [passwordMessage, setPasswordMessage] = useState("");
  const [savingProfile, setSavingProfile] = useState(false);
  const [savingPassword, setSavingPassword] = useState(false);

  useEffect(() => {
    fanApi.me()
      .then(({ user: loadedUser }) => {
        setUser(loadedUser);
        setName(loadedUser.name);
        setPreview(loadedUser.avatar_url);
      })
      .catch(() => navigate("/login", { replace: true }));
  }, [navigate]);

  const saveProfile = async (event: FormEvent) => {
    event.preventDefault();
    setProfileMessage("");
    setSavingProfile(true);

    try {
      const form = new FormData();
      form.append("name", name);
      if (avatar) form.append("avatar", avatar);
      if (removeAvatar) form.append("remove_avatar", "1");

      const result = await fanApi.updateProfile(form);
      setUser(result.user);
      setPreview(result.user.avatar_url);
      setAvatar(null);
      setRemoveAvatar(false);
      setProfileMessage(result.message);
    } catch (reason) {
      setProfileMessage(reason instanceof Error ? reason.message : "Profile update failed.");
    } finally {
      setSavingProfile(false);
    }
  };

  const savePassword = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setPasswordMessage("");
    setSavingPassword(true);

    try {
      const form = new FormData(event.currentTarget);
      const result = await fanApi.updatePassword(
        String(form.get("current_password") ?? ""),
        String(form.get("password") ?? ""),
        String(form.get("password_confirmation") ?? ""),
      );
      setPasswordMessage(result.message);
      setUser((current) => current ? { ...current, has_password: true } : current);
      event.currentTarget.reset();
    } catch (reason) {
      setPasswordMessage(reason instanceof Error ? reason.message : "Password update failed.");
    } finally {
      setSavingPassword(false);
    }
  };

  const initials = user?.name.split(/\s+/).map((part) => part[0]).join("").slice(0, 2).toUpperCase();

  return (
    <main className="mx-auto max-w-2xl px-container-margin pb-32 pt-28">
      <Reveal direction="up">
        <Link to="/profile" className="mb-lg inline-flex items-center gap-xs text-secondary hover:text-primary">
          <Icon name="arrow_back" />
          Back to profile
        </Link>

        <form onSubmit={(event) => void saveProfile(event)} className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-xl">
          <h1 className="font-headline-lg text-headline-lg">Edit profile</h1>

          <div className="mt-lg flex flex-col items-center gap-md sm:flex-row">
            <div className="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-outline-variant bg-surface-container-high text-xl font-bold text-primary">
              {preview ? <img src={preview} alt="" className="h-full w-full object-cover" /> : initials}
            </div>
            <div className="flex flex-wrap justify-center gap-sm sm:justify-start">
              <label className="cursor-pointer rounded-full bg-primary-container px-md py-sm font-bold text-on-primary-container">
                Choose photo
                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  className="hidden"
                  onChange={(event) => {
                    const file = event.target.files?.[0];
                    if (!file) return;
                    setAvatar(file);
                    setRemoveAvatar(false);
                    setPreview(URL.createObjectURL(file));
                  }}
                />
              </label>
              {preview && (
                <button type="button" onClick={() => { setAvatar(null); setPreview(null); setRemoveAvatar(true); }} className="rounded-full border border-outline-variant px-md py-sm text-secondary hover:text-error">
                  Remove photo
                </button>
              )}
            </div>
          </div>

          <label className="mt-lg block">
            <span className="mb-xs block font-label-md text-label-md uppercase tracking-widest text-secondary">Display name</span>
            <Input required value={name} onChange={(event) => setName(event.target.value)} maxLength={255} />
          </label>

          {profileMessage && <p role="status" className="mt-md text-body-md text-secondary">{profileMessage}</p>}
          <Button type="submit" className="mt-lg w-full" disabled={savingProfile || !name.trim()}>
            {savingProfile ? "Saving…" : "Save profile"}
          </Button>
        </form>

        <form onSubmit={(event) => void savePassword(event)} className="mt-lg rounded-2xl border border-outline-variant/20 bg-surface-container-low p-xl">
          <h2 className="font-headline-md text-headline-md">{user?.has_password ? "Change password" : "Set password"}</h2>
          <div className="mt-lg space-y-md">
            {user?.has_password && (
              <label className="block">
                <span className="mb-xs block font-label-md text-label-md uppercase tracking-widest text-secondary">Current password</span>
                <Input required name="current_password" type="password" autoComplete="current-password" />
              </label>
            )}
            <label className="block">
              <span className="mb-xs block font-label-md text-label-md uppercase tracking-widest text-secondary">New password</span>
              <Input required name="password" type="password" minLength={8} autoComplete="new-password" />
            </label>
            <label className="block">
              <span className="mb-xs block font-label-md text-label-md uppercase tracking-widest text-secondary">Confirm new password</span>
              <Input required name="password_confirmation" type="password" minLength={8} autoComplete="new-password" />
            </label>
          </div>

          {passwordMessage && <p role="status" className="mt-md text-body-md text-secondary">{passwordMessage}</p>}
          <Button type="submit" className="mt-lg w-full" disabled={savingPassword}>
            {savingPassword ? "Saving…" : user?.has_password ? "Change password" : "Set password"}
          </Button>
        </form>
      </Reveal>
    </main>
  );
}
