const data = [
    {
        id: "home",
        icon: "iconsminds-shop",
        label: "Dashboard",
        to: "/app/home",
        middleware: 2
    },
    {
        id: "website",
        icon: "iconsminds-digital-drawing",
        label: "Event Manager",
        to: "/app/website",
        middleware: 2,
        subs: [
            {
                icon: "simple-icon-layers",
                label: "Event Details",
                to: "/app/website/domain"
            },
            {
                icon: "simple-icon-pin",
                label: "Attendee Settings",
                to: "/app/website/regedit"
            },
            {
                icon: "simple-icon-pin",
                label: "Cart Settings",
                to: "/app/website/cart"
            }
        ]
    },
    {
        id: "wizard",
        icon: "iconsminds-digital-drawing",
        label: "Expo Manager",
        to: "/app/wizard/booth/main",
        middleware: 2,
        subs: [
            {
                id: "booth",
                icon: "simple-icon-grid",
                label: "Exhibit Booth",
                to: "/app/wizard/booth",   
                subs: [
                    {
                        icon: "simple-icon-briefcase",
                        label: "Wizard",
                        to: "/app/wizard/booth/main"
                    },
                    {
                        icon: "simple-icon-basket-loaded",
                        label: "Manage Templates",
                        to: "/app/wizard/booth/models"
                    },
                    {
                        icon: "simple-icon-grid",
                        label: "Booth Management",
                        to: "/app/wizard/booth/view",
                    },
                    {
                        icon: "iconsminds-male-female",
                        label: "User Management",
                        to: "/app/wizard/booth/users",
                    },
                    {
                        icon: "simple-icon-settings",
                        label: "Booth Setting",
                        to: "/app/wizard/booth/setting",
                    }
                ]
            },
            {
                id: "sponsor",
                icon: "simple-icon-badge",
                label: "Sponsor Booth",
                to: "/app/wizard/sponsor",   
                subs: [
                    {
                        icon: "simple-icon-briefcase",
                        label: "Wizard",
                        to: "/app/wizard/sponsor/main"
                    },
                    {
                        icon: "simple-icon-basket-loaded",
                        label: "Manage Templates",
                        to: "/app/wizard/sponsor/models"
                    },
                    {
                        icon: "simple-icon-grid",
                        label: "Sponsor Management",
                        to: "/app/wizard/sponsor/view",
                    },
                    {
                        icon: "iconsminds-male-female",
                        label: "User Management",
                        to: "/app/wizard/sponsor/users",
                    },
                    {
                        icon: "simple-icon-settings",
                        label: "Sponsor Setting",
                        to: "/app/wizard/sponsor/setting",
                    }
                ]
            },
            {
                id: "poster",
                icon: "simple-icon-map",
                label: "Poster Presentation",
                to: "/app/wizard/poster",   
                subs: [
                    {
                        icon: "simple-icon-briefcase",
                        label: "Wizard",
                        to: "/app/wizard/poster/main"
                    },
                    {
                        icon: "simple-icon-basket-loaded",
                        label: "Poster Categories",
                        to: "/app/wizard/poster/category"
                    },
                    {
                        icon: "simple-icon-basket-loaded",
                        label: "Poster Management",
                        to: "/app/wizard/poster/models"
                    },
                    {
                        icon: "simple-icon-grid",
                        label: "User Management",
                        to: "/app/wizard/poster/users"
                    },
                    {
                        icon: "simple-icon-settings",
                        label: "Poster Setting",
                        to: "/app/wizard/poster/setting"
                    }
                ]
            }
        ]
    },
    {
        id: "tracks",
        icon: "simple-icon-camrecorder",
        label: "Session Manager",
        to: "/tracks",
        middleware: 2,
        subs: [
            {
                icon: "simple-icon-grid",
                label: "Agenda Manager",
                to: "/tracks/manager",
            },
            {
                icon: "simple-icon-badge",
                label: "Agenda Tracks",
                to: "/tracks/category",
            },
            {
                icon: "simple-icon-badge",
                label: "Agenda Sessions",
                to: "/tracks/session",
            },
            {
                icon: "simple-icon-map",
                label: "Video Sessions",
                to: "/tracks/page"
            },
            {
                icon: "simple-icon-map",
                label: "Video Settings",
                to: "/tracks/video",
            }
        ]
    },
    {
        id: "users",
        icon: "simple-icon-user-following",
        label: "User Manager",
        to: "/app/users",
        middleware: 2,
        subs: [
            {
                icon: "iconsminds-king-2",
                label: "Admin Users",
                to: "/app/users/admin"
            },
            {
                icon: "iconsminds-mens",
                label: "Front Users",
                to: "/app/users/user"
            },
            {
                icon: "iconsminds-network",
                label: "Booth Users",
                to: "/app/users/booth"
            },
            {
                icon: "iconsminds-network",
                label: "Sponsor Users",
                to: "/app/users/sponsor"
            },
            {
                icon: "iconsminds-network",
                label: "Poster Users",
                to: "/app/users/poster"
            },
            {
                icon: "iconsminds-network",
                label: "Event Stuff",
                to: "/app/users/event"
            },
            {
                icon: "simple-icon-people",
                label: "All Users",
                to: "/app/users/all"
            }
        ]
    },
    {
        id: "manager-wizard",
        icon: "iconsminds-digital-drawing",
        label: "Wizard",
        to: "/wizard/booth/",
        only: 3,
        subs: [
            // {
            //     id: "booth",
            //     icon: "simple-icon-grid",
            //     label: "Exhibit Booth",
            //     to: "/wizard/booth/main",
            //     unique: 3
            // },
            {
                id: "booth",
                icon: "simple-icon-grid",
                label: "Booth Management",
                to: "/wizard/booth/view",
                unique: 3
            },
            // {
            //     id: "sponsor",
            //     icon: "simple-icon-badge",
            //     label: "Exhibit Sponsor",
            //     to: "/wizard/sponsor/main",
            //     unique: 3 
            // },
            {
                id: "sponsor",
                icon: "simple-icon-grid",
                label: "Sponsor Management",
                to: "/wizard/sponsor/view",
                unique: 3 
            },
            // {
            //     id: "poster",
            //     icon: "simple-icon-map",
            //     label: "Poster Presentation",
            //     to: "/wizard/poster/main",
            //     unique: 3
            // },
            {
                id: "poster",
                icon: "simple-icon-basket-loaded",
                label: "Poster Management",
                to: "/wizard/poster/models",
                unique: 3
            },
            {
                id: "team",
                icon: "simple-icon-people",
                label: "Team",
                to: "/wizard/team"
            },
        ]
    },
    {
        id: "dev-wizard",
        icon: "iconsminds-digital-drawing",
        label: "Wizard",
        to: "/wizard/booth/",
        only: 4,
        subs: [
            {
                id: "booth",
                icon: "simple-icon-grid",
                label: "Exhibit Booth",
                to: "/wizard/booth",
                unique: 4
            },
            {
                id: "sponsor",
                icon: "simple-icon-badge",
                label: "Exhibit Sponsor",
                to: "/wizard/sponsor",
                unique: 4
            },
            {
                id: "poster",
                icon: "simple-icon-map",
                label: "Poster Presentation",
                to: "/wizard/poster",
                unique: 4
            }
        ]
    },
    {
        id: "network",
        icon: "simple-icon-people",
        label: "Networking",
        to: "/wizard/network"
    },
    {
        id: "chat",
        icon: "iconsminds-speach-bubbles",
        label: "War Room",
        to: "/app/warroom",
        middleware: 2
    },
    {
        id: "account",
        icon: "simple-icon-user",
        label: "Settings",
        to: "/app/settings",
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
]

export default data;

// analtyicssimple-icon-graph