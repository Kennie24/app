import { useMemo, useState } from "react";
import { getCountries, getCountryCallingCode, parsePhoneNumberFromString, type CountryCode } from "libphonenumber-js";
import { Icon } from "@/components/Icon";

const displayNames = new Intl.DisplayNames(["en"], { type: "region" });

const countries = getCountries()
  .map((code) => ({
    code,
    name: displayNames.of(code) ?? code,
    dialCode: `+${getCountryCallingCode(code)}`,
  }))
  .sort((a, b) => a.name.localeCompare(b.name));

type CountryPhoneInputProps = {
  value: string;
  onChange: (value: string) => void;
  defaultCountry?: CountryCode;
  required?: boolean;
  id?: string;
};

export function CountryPhoneInput({
  value,
  onChange,
  defaultCountry = "UG",
  required = false,
  id = "phone",
}: CountryPhoneInputProps) {
  const [selected, setSelected] = useState<CountryCode>(defaultCountry);
  const [open, setOpen] = useState(false);
  const [search, setSearch] = useState("");
  const country = countries.find((item) => item.code === selected) ?? countries[0];
  const internationalNumber = parsePhoneNumberFromString(value, selected)?.number ?? `${country.dialCode}${value.replace(/\D/g, "")}`;

  const filtered = useMemo(() => {
    const query = search.trim().toLowerCase();
    if (!query) return countries;
    return countries.filter((item) =>
      item.name.toLowerCase().includes(query) ||
      item.code.toLowerCase().includes(query) ||
      item.dialCode.includes(query)
    );
  }, [search]);

  return (
    <div className="relative">
      <div className="flex h-14 items-center rounded-xl border border-transparent bg-surface-container-high transition-all focus-within:border-primary focus-within:ring-1 focus-within:ring-primary">
        <button
          type="button"
          onClick={() => setOpen((current) => !current)}
          aria-haspopup="listbox"
          aria-expanded={open}
          className="flex h-full shrink-0 items-center gap-xs border-r border-surface-container-highest px-sm text-on-surface hover:bg-white/5 sm:px-md"
        >
          <span className={`fi fi-${country.code.toLowerCase()} text-xl`} aria-hidden />
          <span className="font-body-md text-body-md">{country.dialCode}</span>
          <Icon name="expand_more" className={`text-[18px] text-secondary transition-transform ${open ? "rotate-180" : ""}`} />
        </button>
        <input
          id={id}
          required={required}
          type="tel"
          inputMode="tel"
          autoComplete="tel-national"
          value={value}
          onChange={(event) => onChange(event.target.value)}
          placeholder="Phone number"
          className="min-w-0 flex-1 bg-transparent px-md py-sm font-body-lg text-body-lg text-on-surface outline-none placeholder:text-outline"
        />
      </div>

      {open && (
        <>
          <button type="button" aria-label="Close country list" onClick={() => setOpen(false)} className="fixed inset-0 z-[70] cursor-default" />
          <div className="absolute left-0 right-0 top-[calc(100%+8px)] z-[80] overflow-hidden rounded-xl border border-outline-variant/30 bg-surface-container-lowest shadow-2xl">
            <div className="flex items-center gap-sm border-b border-outline-variant/20 p-sm">
              <Icon name="search" className="text-[20px] text-secondary" />
              <input
                autoFocus
                value={search}
                onChange={(event) => setSearch(event.target.value)}
                placeholder="Search country or code"
                className="min-w-0 flex-1 bg-transparent py-xs text-body-md text-on-surface outline-none placeholder:text-outline"
              />
              <span className="font-label-sm text-label-sm text-secondary">{filtered.length}</span>
            </div>
            <ul role="listbox" className="max-h-72 overflow-y-auto py-xs">
              {filtered.map((item) => (
                <li key={item.code}>
                  <button
                    type="button"
                    role="option"
                    aria-selected={item.code === selected}
                    onClick={() => {
                      setSelected(item.code);
                      setOpen(false);
                      setSearch("");
                    }}
                    className={`flex w-full items-center gap-sm px-md py-sm text-left transition-colors hover:bg-surface-container-high ${item.code === selected ? "bg-primary/10" : ""}`}
                  >
                    <span className={`fi fi-${item.code.toLowerCase()} w-7 text-xl`} aria-hidden />
                    <span className="min-w-0 flex-1 truncate font-body-md text-body-md text-on-surface">{item.name}</span>
                    <span className="font-body-md text-body-md text-secondary">{item.dialCode}</span>
                    <span className="w-7 font-label-sm text-label-sm text-outline">{item.code}</span>
                  </button>
                </li>
              ))}
            </ul>
          </div>
        </>
      )}
      <input type="hidden" name="country" value={country.code} />
      <input type="hidden" name="dial_code" value={country.dialCode} />
      <input type="hidden" name="phone_e164" value={internationalNumber} />
    </div>
  );
}
