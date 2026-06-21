import { Icon } from "@/components/Icon";
import { Reveal, StaggerGroup, StaggerItem } from "@/components/Reveal";

const activity = [
  { id: "#TK-88219", time: "Today, 2:42 PM", status: "Success" },
  { id: "#TK-88218", time: "Today, 2:38 PM", status: "Success" },
  { id: "#TK-88217", time: "Today, 2:15 PM", status: "Invalid" },
  { id: "#TK-88216", time: "Yesterday, 11:55 PM", status: "Success" },
];

export function AdminDashboard() {
  return (
    <main className="pt-24 pb-32 px-container-margin space-y-lg max-w-6xl mx-auto">
      <Reveal as="section" direction="down" className="py-sm">
        <h2 className="font-headline-lg-mobile text-headline-lg-mobile text-on-surface">
          Admin Dashboard
        </h2>
        <p className="font-body-md text-body-md text-secondary">Real-time redemption monitoring</p>
      </Reveal>

      {/* Stats */}
      <StaggerGroup className="grid grid-cols-2 gap-gutter" stagger={0.1}>
        <StaggerItem direction="up" className="col-span-2">
          <div className="bento-card p-md rounded-xl flex flex-col justify-between h-32 relative overflow-hidden">
            <div className="z-10">
              <p className="font-label-md text-label-md text-secondary">TOTAL REDEMPTIONS</p>
              <h3 className="font-display-lg text-display-lg text-primary">12,842</h3>
            </div>
            <div className="absolute right-[-20px] bottom-[-20px] opacity-10">
              <Icon name="token" className="text-[120px]" />
            </div>
            <div className="flex items-center gap-1 text-primary text-label-sm font-label-sm">
              <Icon name="trending_up" className="text-[14px]" />
              <span>+12% from last week</span>
            </div>
          </div>
        </StaggerItem>

        <StaggerItem direction="up">
          <div className="bento-card p-md rounded-xl space-y-xs">
            <div className="flex items-center gap-2 text-secondary">
              <Icon name="album" className="text-[20px]" />
              <p className="font-label-md text-label-md">ACTIVE ASSETS</p>
            </div>
            <h3 className="font-headline-md text-headline-md text-on-surface">1,402</h3>
          </div>
        </StaggerItem>

        <StaggerItem direction="up">
          <div className="bento-card p-md rounded-xl space-y-xs">
            <div className="flex items-center gap-2 text-error">
              <Icon name="report" className="text-[20px] pulse-error" />
              <p className="font-label-md text-label-md">FAILED</p>
            </div>
            <h3 className="font-headline-md text-headline-md text-error">42</h3>
          </div>
        </StaggerItem>
      </StaggerGroup>

      {/* Chart */}
      <Reveal as="section" direction="up" duration={0.8}>
        <div className="bento-card p-md rounded-xl space-y-md">
          <div className="flex justify-between items-center">
            <h4 className="font-label-md text-label-md text-on-surface">REDEMPTION TRENDS</h4>
            <div className="flex gap-2">
              <span className="px-2 py-1 bg-primary/10 text-primary rounded-full text-label-sm font-label-sm">
                7D
              </span>
              <span className="px-2 py-1 text-secondary rounded-full text-label-sm font-label-sm">
                30D
              </span>
            </div>
          </div>
          <div className="relative h-[180px] w-full">
            <svg viewBox="0 0 400 150" className="w-full h-full" preserveAspectRatio="none">
              <line stroke="#282828" strokeWidth="1" x1="0" x2="400" y1="0" y2="0" />
              <line stroke="#282828" strokeWidth="1" x1="0" x2="400" y1="50" y2="50" />
              <line stroke="#282828" strokeWidth="1" x1="0" x2="400" y1="100" y2="100" />
              <line stroke="#282828" strokeWidth="1" x1="0" x2="400" y1="150" y2="150" />
              <defs>
                <linearGradient id="chartGradient" x1="0%" x2="0%" y1="0%" y2="100%">
                  <stop offset="0%" style={{ stopColor: "#1db954", stopOpacity: 0.3 }} />
                  <stop offset="100%" style={{ stopColor: "#1db954", stopOpacity: 0 }} />
                </linearGradient>
              </defs>
              <path
                d="M0,150 L0,120 C40,110 60,130 100,90 C140,50 180,100 220,70 C260,40 300,60 340,30 C380,0 400,20 400,20 L400,150 Z"
                fill="url(#chartGradient)"
              />
              <path
                d="M0,120 C40,110 60,130 100,90 C140,50 180,100 220,70 C260,40 300,60 340,30 C380,0 400,20 400,20"
                fill="none"
                stroke="#1db954"
                strokeLinecap="round"
                strokeWidth="3"
              />
              <circle cx="340" cy="30" fill="#1db954" r="4" />
              <circle cx="340" cy="30" fill="#1db954" opacity="0.2" r="8" />
            </svg>
          </div>
          <div className="flex justify-between font-label-sm text-label-sm text-secondary pt-2">
            {["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"].map((d) => (
              <span key={d}>{d}</span>
            ))}
          </div>
        </div>
      </Reveal>

      {/* Recent activity */}
      <section className="space-y-md">
        <Reveal>
          <div className="flex justify-between items-center px-xs">
            <h4 className="font-label-md text-label-md text-on-surface">RECENT ACTIVITY</h4>
            <button className="text-primary font-label-md text-label-md hover:underline">
              View All
            </button>
          </div>
        </Reveal>
        <StaggerGroup className="space-y-sm" stagger={0.07}>
          {activity.map((a) => {
            const isError = a.status === "Invalid";
            return (
              <StaggerItem key={a.id} direction="left">
                <div className="bento-card p-md rounded-xl flex items-center justify-between">
                  <div className="flex items-center gap-md">
                    <div className="w-10 h-10 bg-surface-container-high rounded-lg flex items-center justify-center">
                      <Icon
                        name={isError ? "block" : "confirmation_number"}
                        className={isError ? "text-error" : "text-primary"}
                      />
                    </div>
                    <div>
                      <p className="font-body-lg text-body-lg text-on-surface font-bold">{a.id}</p>
                      <p className="font-label-sm text-label-sm text-secondary">{a.time}</p>
                    </div>
                  </div>
                  <span
                    className={
                      isError
                        ? "px-2 py-1 bg-error-container text-error rounded text-label-sm font-label-sm"
                        : "px-2 py-1 bg-surface-container text-primary rounded text-label-sm font-label-sm"
                    }
                  >
                    {a.status}
                  </span>
                </div>
              </StaggerItem>
            );
          })}
        </StaggerGroup>
      </section>
    </main>
  );
}
