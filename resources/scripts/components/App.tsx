import React from 'react';
import { hot } from 'react-hot-loader/root';
import { Route, Router, Switch } from 'react-router-dom';
import { StoreProvider } from 'easy-peasy';
import { store } from '@/state';
import DashboardRouter from '@/routers/DashboardRouter';
import ServerRouter from '@/routers/ServerRouter';
import AuthenticationRouter from '@/routers/AuthenticationRouter';
import { SiteSettings } from '@/state/settings';
import ProgressBar from '@/components/elements/ProgressBar';
import { NotFound } from '@/components/elements/ScreenBlock';
import tw, { GlobalStyles as TailwindGlobalStyles } from 'twin.macro';
import GlobalStylesheet from '@/assets/styles/GlobalStylesheet';
import AnnouncementsRouter from '@/routers/AnnouncementsRouter';
import KnowledgebaseRouter from '@/routers/KnowledgebaseRouter';
import StoreRouter from '@/routers/StoreRouter';
import StaffRouter from '@/routers/StaffRouter';
import { history } from '@/components/history';
import { setupInterceptors } from '@/api/interceptors';
import AuthenticatedRoute from '@/components/elements/AuthenticatedRoute';
import { ServerContext } from '@/state/server';

interface ExtendedWindow extends Window {
    SiteConfiguration?: SiteSettings;
    PterodactylUser?: {
        uuid: string;
        username: string;
        email: string;
        /* eslint-disable camelcase */
        root_admin: boolean;
        use_totp: boolean;
        staff: 0;
        name_first: string;
        name_last: string;
        discord_id: string;
        discord_name: string;
        github_id: string;
        github_name: string;
        language: string;
        oauth: string;
        updated_at: string;
        created_at: string;
        /* eslint-enable camelcase */
    };
}

setupInterceptors(history);

const App = () => {
    const { PterodactylUser, SiteConfiguration } = (window as ExtendedWindow);
    if (PterodactylUser && !store.getState().user.data) {
        store.getActions().user.setUserData({
            uuid: PterodactylUser.uuid,
            username: PterodactylUser.username,
            email: PterodactylUser.email,
            language: PterodactylUser.language,
            rootAdmin: PterodactylUser.root_admin,
            useTotp: PterodactylUser.use_totp,
            staff: PterodactylUser.staff,
            name_first: PterodactylUser.name_first,
            name_last: PterodactylUser.name_last,
            discordID: PterodactylUser.discord_id,
            discordName: PterodactylUser.discord_name,
            githubID: PterodactylUser.github_id,
            githubName: PterodactylUser.github_name,
            oauth: PterodactylUser.oauth,
            createdAt: new Date(PterodactylUser.created_at),
            updatedAt: new Date(PterodactylUser.updated_at),
        });
    }

    if (!store.getState().settings.data) {
        store.getActions().settings.setSettings(SiteConfiguration!);
    }

    return (
        <>
            <GlobalStylesheet/>
            <TailwindGlobalStyles/>
            <StoreProvider store={store}>
                <ProgressBar/>
                <div css={tw`mx-auto w-auto`}>
                    <Router history={history}>
                        <Switch>
                            <Route path={'/auth'}>
                                <AuthenticationRouter/>
                            </Route>
                            <AuthenticatedRoute path={'/server/:id'}>
                                <ServerContext.Provider>
                                    <ServerRouter/>
                                </ServerContext.Provider>
                            </AuthenticatedRoute>
                            {PterodactylUser?.staff &&
                                <AuthenticatedRoute path={'/staff'}>
                                    <StaffRouter/>
                                </AuthenticatedRoute>
                            }
                            <AuthenticatedRoute path={'/announcements'}>
                                <AnnouncementsRouter/>
                            </AuthenticatedRoute>
                            <AuthenticatedRoute path={'/knowledgebase'}>
                                <KnowledgebaseRouter/>
                            </AuthenticatedRoute>
                            <AuthenticatedRoute path={'/store'}>
                                <StoreRouter/>
                            </AuthenticatedRoute>
                            <AuthenticatedRoute path={'/'}>
                                <DashboardRouter/>
                            </AuthenticatedRoute>
                            <Route path={'*'}>
                                <NotFound/>
                            </Route>
                        </Switch>
                    </Router>
                </div>
            </StoreProvider>
        </>
    );
};

export default hot(App);
