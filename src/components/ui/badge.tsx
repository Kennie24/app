import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/utils";

const badgeVariants = cva(
  "inline-flex items-center gap-1 rounded-full px-2 py-1 font-label-md text-label-sm uppercase tracking-widest",
  {
    variants: {
      variant: {
        default: "bg-surface-container text-primary",
        success: "bg-primary/15 text-primary",
        warning: "bg-tertiary-container/40 text-tertiary",
        error: "bg-error-container text-error",
        muted: "bg-surface-container-high text-secondary",
      },
    },
    defaultVariants: { variant: "default" },
  }
);

export interface BadgeProps
  extends React.HTMLAttributes<HTMLSpanElement>,
    VariantProps<typeof badgeVariants> {}

export function Badge({ className, variant, ...props }: BadgeProps) {
  return <span className={cn(badgeVariants({ variant }), className)} {...props} />;
}
