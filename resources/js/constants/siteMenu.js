const data = [
    {
        id: "home",
        icon: "simple-icon-home",
        label: "Home",
        to: "/"
    },
    {
        id: "session",
        icon: "iconsminds-video-tripod",
        label: "Sessions",
        to: "/session"
    },
    {
        id: "exhibit",
        icon: "iconsminds-life-safer",
        label: "Exhibit",
        to: "/exhibit",
        subs: [
            {
                icon: "simple-icon-puzzle",
                label: "Booth",
                to: "/exhibit/booth"
            },
            {
                icon: "simple-icon-tag",
                label: "Sponsor",
                to: "/exhibit/sponsor"
            },
            {
                icon: "simple-icon-paper-clip",
                label: "Poster",
                to: "/exhibit/poster"
            }
        ]
    },
    {
        id: "network",
        icon: "iconsminds-handshake",
        label: "Networking",
        to: "/network"
    }
]

export default data