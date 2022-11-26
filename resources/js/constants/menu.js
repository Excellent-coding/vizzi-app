
const data = [
  {
      id: "piaf",
      icon: "iconsminds-shop",
      label: "Dashboard",
      to: "/app/home",
      middleware: 1
  },
  {
    id: "website",
    icon: "iconsminds-digital-drawing",
    label: "Event Manager",
    to: "/app/website",
    middleware: 2
  },
  {
    id: "users",
    icon: "simple-icon-user",
    label: "Users",
    to: "/app/users",
    middleware: 1,
    subs: [
      {
        icon: "simple-icon-user-following",
        id: "users-admin",
        label: "Admin",
        to: "/app/users/admin"
      }, {
        icon: "simple-icon-people",
        id: "users-user",
        label: "User",
        to: "/app/users/user"
      }
    ]
  },
  {
    id: "account",
    icon: "simple-icon-user",
    label: "Settings",
    to: "/settings",
    middleware: 2,
    subs: [ 
      {
        icon: "simple-icon-equalizer",
        label: "Profile",
        to: "/app/settings/profile"
      },
      {
        icon: "simple-icon-user-following",
        label: "Password",
        to: "/app/settings/password"
      }
    ]
  }
];

export default data;
