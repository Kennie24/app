import { Routes, Route, Navigate } from "react-router-dom";
import { AppShell } from "@/components/AppShell";
import { Landing } from "@/pages/Landing";
import { ScanToRedeem } from "@/pages/ScanToRedeem";
import { RedeemYourEP } from "@/pages/RedeemYourEP";
import { DownloadReady } from "@/pages/DownloadReady";
import { TokenStatus } from "@/pages/TokenStatus";
import { AdminDashboard } from "@/pages/AdminDashboard";
import { Profile } from "@/pages/Profile";
import { ProfileSettings } from "@/pages/ProfileSettings";
import { Login } from "@/pages/Login";
import { Signup } from "@/pages/Signup";
import { ArtistLogin } from "@/pages/ArtistLogin";
import { ArtistStudioRedirect } from "@/pages/ArtistStudioRedirect";
import { Store } from "@/pages/Store";
import { ReleaseDetail } from "@/pages/ReleaseDetail";
import { Discover } from "@/pages/Discover";
import { Artists } from "@/pages/Artists";
import { Community } from "@/pages/Community";
import { Checkout } from "@/pages/Checkout";

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Landing />} />
      <Route path="/login" element={<Login />} />
      <Route path="/signup" element={<Signup />} />
      <Route path="/artist/login" element={<ArtistLogin />} />
      <Route path="/artist" element={<ArtistStudioRedirect />} />
      <Route path="/discover" element={<Discover />} />
      <Route path="/store" element={<Store />} />
      <Route path="/store/:key" element={<ReleaseDetail />} />
      <Route path="/artists" element={<Artists />} />
      <Route path="/community" element={<Community />} />
      <Route path="/checkout/:key" element={<Checkout />} />
      <Route element={<AppShell />}>
        <Route path="/scan" element={<ScanToRedeem />} />
        <Route path="/redeem" element={<RedeemYourEP />} />
        <Route path="/download" element={<DownloadReady />} />
        <Route path="/token" element={<TokenStatus />} />
        <Route path="/admin" element={<AdminDashboard />} />
        <Route path="/profile" element={<Profile />} />
        <Route path="/profile/settings" element={<ProfileSettings />} />
      </Route>
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
}
