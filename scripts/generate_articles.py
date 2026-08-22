from pathlib import Path
from html import escape
from datetime import date, timedelta
import json, re
ROOT=Path(__file__).resolve().parents[1]
services=[
('website-design','Website Design','a clear, fast website that turns local searches into qualified conversations','mobile-first layouts, accessible navigation, conversion-focused calls to action, and maintainable code','/services/Website-Design-and-Development/index.html','photo-1467232004584-a241de8bcf5d'),
('managed-it','Managed IT Support','dependable technology with fewer interruptions and a practical support plan','device monitoring, routine maintenance, user support, security updates, and technology planning','/services/Computer-Services/Business/msp-service.html','photo-1558494949-ef010cbdcc31'),
('computer-repair','Computer Repair','fast diagnosis and sensible repair options for the computers your work depends on','hardware diagnostics, malware cleanup, performance tuning, upgrades, and straightforward recommendations','/services/Computer-Services/business-and-residential/computer-repair-service.html','photo-1593642532400-2682810df593'),
('wifi-networking','Network and Wi-Fi Service','stable, secure connectivity across offices, shops, and customer areas','coverage assessment, router placement, secure configuration, guest access, and troubleshooting','/services/Computer-Services/business-and-residential/network-and-wireless-service.html','photo-1544197150-b99a580bb7a8'),
('data-backup','Data Backup and Cloning','a recoverable copy of important business information before a failure becomes a crisis','backup planning, drive cloning, restore checks, storage organization, and recovery guidance','/services/Computer-Services/business-and-residential/data-backup-and-cloning.html','photo-1568438350562-2cae6d394ad0'),
('security-cameras','Security Camera Installation','useful visibility around entrances, work areas, inventory, and property','camera placement planning, recording setup, remote viewing, network configuration, and user training','/services/security-cameras/index.html','photo-1557597774-9d273605dfa9'),
('vhs-digitization','VHS and Media Digitization','digital copies that make family, community, and business recordings easier to preserve and share','careful media handling, analog-to-digital conversion, organized files, and practical delivery options','/services/Video-and-Film-Digitization/vhs-to-digital-conversion.html','photo-1574717024653-61fd2cf4d44d'),
('videography','Videography','professional video that explains a business, documents an event, or demonstrates a service','production planning, recording, editing, pacing, titles, and delivery for web and social use','/services/Videography/index.html','photo-1485846234645-a62644f84728'),
('crm-development','Custom CRM Development','a workflow-focused system that keeps customer details and follow-up work organized','process discovery, custom fields, useful dashboards, permissions, automation, and staff-friendly interfaces','/services/CRM/index.html','photo-1551288049-bebda4e38f71'),
('wordpress-plugins','Custom WordPress Plugins','purpose-built site functionality without forcing the business into an awkward generic tool','requirements planning, secure development, testing, documentation, and maintainable integrations','/services/WordPress-Plugins/index.html','photo-1461749280684-dccba630e2f6'),
]
cities=[
('Glasgow','Barren County','South Central Kentucky','Park City and Hiseville'),
('Bowling Green','Warren County','South Central Kentucky','Plano and Alvaton'),
('Cave City','Barren County','the Caveland area','Horse Cave and Park City'),
]
angles=[
('A Practical Guide for Local Businesses','planning','choose the right scope before spending money'),
('What Small Businesses Should Know','essentials','separate the essentials from features that can wait'),
('How to Build a Smarter Technology Plan','strategy','connect today’s project to the next stage of growth'),
('Common Problems and How to Prevent Them','prevention','reduce avoidable downtime, confusion, and rework'),
]
# 10 services x 3 cities x 4 editorial angles = 120 unique possibilities.
combos=[(s,c,a) for a in angles for c in cities for s in services][:100]
assert len(combos)==100 and len(set((x[0][0],x[1][0],x[2][0]) for x in combos))==100

def slugify(x): return re.sub(r'[^a-z0-9]+','-',x.lower()).strip('-')

PUBLICATION_FILE=ROOT/'articles'/'publication-dates.json'
today=date.today()
if PUBLICATION_FILE.exists():
 publication_dates=json.loads(PUBLICATION_FILE.read_text())
else:
 publication_dates={}
for i,(service,city,angle) in enumerate(combos):
 slug=f'{slugify(service[1])}-{slugify(city[0])}-{slugify(angle[0])}'
 publication_dates.setdefault(slug,(today-timedelta(days=len(combos)-i)).isoformat())
if today.isoformat() not in publication_dates.values():
 oldest=min(publication_dates,key=lambda slug:(publication_dates[slug],slug))
 publication_dates[oldest]=today.isoformat()
PUBLICATION_FILE.write_text(json.dumps(publication_dates,indent=2,sort_keys=True)+'\n')

def page(i,s,c,a):
 key,name,outcome,deliverables,url,photo_id=s; city,county,region,nearby=c; angle,focus,goal=a
 title=f'{name} in {city}, KY: {angle}'
 slug=f'{slugify(name)}-{slugify(city)}-{slugify(angle)}'
 published=date.fromisoformat(publication_dates[slug])
 published_text=f'{published.strftime("%B")} {published.day}, {published.year}'
 image=f'https://images.unsplash.com/{photo_id}?auto=format&fit=crop&w=1200&h=675&q=82'
 desc=f'Learn how {city}, Kentucky businesses can plan {name.lower()} with practical guidance from Warf Designs, serving {county} and nearby communities.'
 sections=[
 (f'Why {name.lower()} matters in {city}',f'''Local companies compete on responsiveness and trust. A customer may discover a business on a phone, call while traveling through {region}, or expect staff to answer a question without searching through several disconnected systems. That makes technology part of the customer experience, even when technology is not what the company sells. For organizations in {city}, {name.lower()} can provide {outcome}. The most useful project is not necessarily the largest one. It is the one tied to a real operational need, an accountable owner, and a result the team can recognize.'''),
 (f'Start with the business problem',f'''Before discussing products or features, write down what is slowing the business today. Note who experiences the problem, how often it occurs, and what a successful day would look like after it is fixed. A retailer may prioritize speed at the counter, while a professional office may value secure access and consistent follow-up. A tourism business serving visitors near {nearby} may need an experience that works well for people who are unfamiliar with the area. This short discovery step prevents a technical purchase from becoming a solution in search of a problem.'''),
 (f'What a thoughtful {focus} project includes',f'''Warf Designs approaches the work around practical components such as {deliverables}. Those pieces should support one another. For example, a polished setup is less valuable when no one knows how to use it, and a powerful feature creates friction when it does not match the team’s daily routine. Ask which tasks happen every day, which information is most important, and which failures would be most disruptive. The answers establish priorities and give the project a useful definition of “done.”'''),
 (f'Plan for local customers and real working conditions',f'''A plan for a {county} organization should reflect its actual audience, building, staffing, and schedule. Local searchers often include a town or “near me” phrase because convenience and service area matter. Employees may divide time between a storefront, home, and customer locations. Seasonal traffic can also change demand. Instead of copying a national company’s setup, build around the distances, devices, internet connections, and customer questions that are common here. That local context makes the finished work easier to use and easier to maintain.'''),
 ('Budget for value, not just the initial price',f'''Compare options by looking at useful life, staff time, support, maintenance, and the cost of leaving the current problem unsolved. The cheapest proposal can become expensive if it requires repeated work or lacks a recovery path. At the same time, a small {city} business should not pay for complexity it cannot use. A phased plan can address the highest-risk or highest-return item first, then add capabilities as results and cash flow justify them. Request a written scope so assumptions, responsibilities, and exclusions are visible before work begins.'''),
 ('Security, privacy, and continuity belong in the plan',f'''Every business project should consider who can access information, how updates are handled, and what happens when equipment or a service fails. Use individual accounts where possible, choose strong unique passwords, enable multifactor authentication when available, and remove access when roles change. Keep important copies separate from the primary device and test the recovery process. These habits are not limited to large corporations. They help Kentucky shops, nonprofits, contractors, and offices protect customer trust while keeping everyday work moving.'''),
 ('How to evaluate a local provider',f'''Look for questions before recommendations. A responsible provider should be willing to learn the workflow, explain tradeoffs in plain language, and identify what the client will need to manage after launch. Ask how changes will be documented, how support requests are handled, and which parts of the solution the business owns. Local service is especially useful when on-site context or a quick conversation matters, but clarity still matters more than proximity alone. The goal is a working relationship with realistic expectations on both sides.'''),
 (f'Next steps for a {city} business',f'''Begin with a short inventory: the current tools, the people involved, the recurring frustration, the desired deadline, and an approximate budget range. Gather two or three examples of what works well and what does not. Then schedule a conversation focused on priorities rather than a predetermined product. Warf Designs serves businesses in {city}, communities across {county}, and neighboring parts of South Central Kentucky. A focused first discussion can clarify whether {name.lower()} is the right next move and what a manageable project would include.''')]
 body='\n'.join(f'<section><h2>{escape(h)}</h2><p>{escape(p)}</p></section>' for h,p in sections)
 words=sum(len(p.split()) for _,p in sections)+80
 schema={"@context":"https://schema.org","@type":"Article","headline":title,"description":desc,"image":image,"datePublished":published.isoformat(),"dateModified":published.isoformat(),"author":{"@type":"Organization","name":"Warf Designs LLC"},"publisher":{"@type":"Organization","name":"Warf Designs LLC"},"about":{"@type":"Service","name":name,"areaServed":city+", Kentucky"}}
 related=[]
 html=f'''<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{escape(title)} | Warf Designs</title><meta name="description" content="{escape(desc,quote=True)}"><link rel="canonical" href="https://warfdesigns.com/articles/{slug}.html">
<meta property="og:type" content="article"><meta property="og:title" content="{escape(title,quote=True)}"><meta property="og:description" content="{escape(desc,quote=True)}"><meta property="og:image" content="{image}"><meta property="article:published_time" content="{published.isoformat()}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Trade+Winds&display=swap" rel="stylesheet"><link rel="stylesheet" href="/css/styles.css"><script type="application/ld+json">{json.dumps(schema)}</script></head>
<body><div id="nav"></div><main class="article-shell"><article class="local-article"><nav class="article-breadcrumb" aria-label="Breadcrumb"><a href="/index.html">Home</a> / <a href="/articles/index.html">Resources</a> / {escape(name)}</nav><header><p class="article-kicker">{escape(city)}, Kentucky business guide</p><h1>{escape(title)}</h1><p class="article-deck">{escape(desc)}</p><p class="article-meta"><time datetime="{published.isoformat()}">Published {published_text}</time> · About 5 minutes</p><img class="article-hero" src="{image}" alt="{escape(name)} planning for a local {city}, Kentucky business" width="1200" height="675" loading="eager"></header>{body}<aside class="article-cta"><h2>Talk through your next step</h2><p>Need practical help with {escape(name.lower())} in {escape(city)} or nearby? Tell Warf Designs what is getting in the way, and we’ll help you identify a sensible path forward.</p><a class="epicbtn2 article-cta-link" href="{url}">Explore {escape(name)}</a> <a class="article-contact-link" href="/pages/contact-us.html">Contact Warf Designs</a></aside><p class="article-disclaimer">This general guide is educational and does not promise a specific technical, security, financial, or search-ranking result. Recommendations depend on the business and its environment.</p></article></main><footer id="footer"></footer><div id="appMenu"></div><script src="/js/script.js"></script></body></html>'''
 return slug,title,desc,html,words,city,name,image,published

items=[]
for i,(s,c,a) in enumerate(combos,1):
 slug,title,desc,html,words,city,name,image,published=page(i,s,c,a)
 (ROOT/'articles'/f'{slug}.html').write_text(html)
 items.append((slug,title,desc,words,city,name,image,published))
# index cards
latest=sorted(items,key=lambda item:(item[7],item[0]),reverse=True)[:10]
cards='\n'.join(f'''<article class="article-card"><a href="/articles/{slug}.html" tabindex="-1"><img class="article-card-image" src="{image}" alt="" width="1200" height="675" loading="lazy"></a><div class="article-card-content"><p class="article-kicker">{escape(city)} · {escape(name)}</p><h2><a href="/articles/{slug}.html">{escape(title)}</a></h2><p>{escape(desc)}</p><p class="article-card-meta"><time datetime="{published.isoformat()}">Published {published.strftime('%B')} {published.day}, {published.year}</time> · About 5 minutes</p></div></article>''' for slug,title,desc,words,city,name,image,published in latest)
index=f'''<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Kentucky Small Business Technology Guides | Warf Designs</title><meta name="description" content="The 10 latest practical website, IT, security, media, CRM, and technology guides for businesses near Glasgow, Bowling Green, and Cave City, Kentucky."><link rel="canonical" href="https://warfdesigns.com/articles/"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Trade+Winds&display=swap" rel="stylesheet"><link rel="stylesheet" href="/css/styles.css"></head><body><div id="nav"></div><main class="article-index"><header class="article-index-header"><p class="article-kicker">Warf Designs resource library</p><h1>Practical Technology Guides for South Central Kentucky Businesses</h1><p>Explore our 10 latest five-minute guides for companies near Glasgow, Bowling Green, Cave City, and surrounding communities.</p></header><div class="article-grid">{cards}</div></main><footer id="footer"></footer><div id="appMenu"></div><script src="/js/script.js"></script></body></html>'''
(ROOT/'articles'/'index.html').write_text(index)
print(f'generated {len(items)} articles and published the latest {len(latest)}; word range {min(x[3] for x in items)}-{max(x[3] for x in items)}')
